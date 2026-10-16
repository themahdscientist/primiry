<?php

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Section as SchoolSection;
use App\Models\Staff;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use RalphJSmit\Filament\Components\Forms\Sidebar;

class CreateMultipleClassrooms extends CreateRecord
{
    protected static string $resource = ClassroomResource::class;

    protected static ?string $title = 'Create Multiple Classrooms';

    public function getBreadcrumb(): string
    {
        return 'Create multiple';
    }

    public function canCreateAnother(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Sidebar::make([
                    Section::make()
                        ->columns()
                        ->schema([
                            Select::make('grades')
                                ->label(__('Grades'))
                                ->multiple()
                                ->options(fn() => Grade::all()->pluck('name', 'id'))
                                ->preload()
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn(Set $set): mixed => $set('sections', []))
                                ->rules(fn(Get $get): array => [
                                    function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                        if (
                                            $this->hasExistingClassrooms(
                                                (array) $value,
                                                (array) $get('sections'),
                                            )
                                        ) {
                                            $fail('One or more selected grade and section combinations already have a classroom.');
                                        }
                                    },
                                ])
                                ->required(),
                            Select::make('sections')
                                ->label(__('Sections'))
                                ->multiple()
                                ->options(function (Get $get): array {
                                    $gradeIds = (array) $get('grades');

                                    return SchoolSection::query()
                                        ->when(
                                            blank($gradeIds),
                                            fn(Builder $query): Builder => $query->whereKey([]),
                                            fn(Builder $query): Builder => $query->whereDoesntHave(
                                                'classrooms',
                                                fn(Builder $classrooms): Builder => $classrooms
                                                    ->whereIn('grade_id', $gradeIds)
                                                    ->where('status', GeneralStatus::Active->value),
                                            ),
                                        )
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all();
                                })
                                ->disabled(fn(Get $get): bool => blank($get('grades')))
                                ->preload()
                                ->searchable()
                                ->required(),
                            TextInput::make('name')
                                ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                ->hintIconTooltip(__('This field is automatically generated based on the selected grade and section.'))
                                ->disabledOn(Operation::Create->value)
                                ->required(fn(string $operation) => $operation !== Operation::Create->value),
                            TextInput::make('capacity')
                                ->integer()
                                ->required(),
                            Select::make('status')
                                ->options(GeneralStatus::class)
                                ->required()
                                ->default(GeneralStatus::Active)
                                ->disabled()
                                ->saved()
                                ->native(false),
                            Select::make('form_teacher_id')
                                ->label(__('Form teacher'))
                                ->relationship('formTeacher', 'id')
                                ->getOptionLabelFromRecordUsing(fn(Staff $record) => $record->user->name)
                                ->searchable()
                                ->preload(),
                        ])
                        ->columnSpanFull(),
                ], [])
                    ->columnSpanFull(),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $gradeIds = (array) ($data['grades'] ?? []);
        $sectionIds = (array) ($data['sections'] ?? []);

        $status = GeneralStatus::Active->value;

        if ($this->hasExistingClassrooms($gradeIds, $sectionIds, $status)) {
            throw ValidationException::withMessages([
                'grades' => 'One or more selected grade and section combinations already have a classroom.',
            ]);
        }

        $capacity = $data['capacity'] ?? null;
        $formTeacherId = $data['form_teacher_id'] ?? null;

        $grades = Grade::query()->whereIn('id', $gradeIds)->get()->keyBy('id');
        $sections = SchoolSection::query()->whereIn('id', $sectionIds)->get()->keyBy('id');
        $formTeacher = $formTeacherId ? Staff::find($formTeacherId) : null;

        $last = null;

        foreach ($grades as $grade) {
            foreach ($sections as $section) {
                $name = trim("{$grade->name} {$section->name}");

                $classroom = Classroom::firstOrNew([
                    'grade_id' => $grade->id,
                    'section_id' => $section->id,
                ]);

                $classroom->name = $name;
                $classroom->capacity = $capacity ?? $classroom->capacity;
                $classroom->status = $status ?? $classroom->status;

                $classroom->grade()->associate($grade);
                $classroom->section()->associate($section);
                if ($formTeacher) {
                    $classroom->formTeacher()->associate($formTeacher);
                } else {
                    if (!$classroom->exists) {
                        $classroom->form_teacher_id = null;
                    }
                }

                if ($parent = $this->getParentRecord()) {
                    $this->associateRecordWithParent($classroom, $parent);
                } else {
                    $classroom->save();
                }

                $last = $classroom;
            }
        }

        return $last ?? new ($this->getModel())();
    }

    /**
     * @param  array<int, int|string>  $gradeIds
     * @param  array<int, int|string>  $sectionIds
     */
    private function hasExistingClassrooms(array $gradeIds, array $sectionIds): bool
    {
        return Classroom::query()
            ->whereIn('grade_id', $gradeIds)
            ->whereIn('section_id', $sectionIds)
            ->where('status', GeneralStatus::Active->value)
            ->exists();
    }
}
