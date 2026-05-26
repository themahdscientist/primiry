<?php

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Staff;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                            TextInput::make('name')
                                ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                ->hintIconTooltip(__('This field is automatically generated based on the selected grade and section.'))
                                ->disabledOn(Operation::Create->value)
                                ->required(fn(string $operation) => $operation !== Operation::Create->value),
                            TextInput::make('capacity')
                                ->integer()
                                ->required(),
                            Select::make('grades')
                                ->label(__('Grades'))
                                ->multiple()
                                ->options(fn() => Grade::all()->pluck('name', 'id'))
                                ->preload()
                                ->searchable()
                                ->required(),
                            Select::make('sections')
                                ->label(__('Sections'))
                                ->multiple()
                                ->options(fn() => \App\Models\Section::all()->pluck('name', 'id'))
                                ->preload()
                                ->searchable()
                                ->required(),
                            Select::make('status')
                                ->options(GeneralStatus::class)
                                ->required()
                                ->default(GeneralStatus::Active)
                                ->native(false),
                            Select::make('form_teacher_id')
                                ->label(__('Form teacher'))
                                ->relationship('formTeacher', 'id', function (Builder $query, ?Classroom $record) {
                                    if ($record?->form_teacher_id) {
                                        $query->where('staff.id', '!=', $record->form_teacher_id);
                                    }

                                    return $query
                                        ->whereHas('user', fn(Builder $q) => $q->where('role_id', Role::STAFF))
                                        ->with('user');
                                })
                                ->getOptionLabelFromRecordUsing(fn(Staff $record) => $record->user->name)
                                // ->getOptionLabelUsing(fn(?Classroom $record) => $record?->formTeacher->user->name)
                                ->preload()
                                ->searchable(),
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

        $capacity = $data['capacity'] ?? null;
        $status = $data['status'] ?? null;
        $formTeacherId = $data['form_teacher_id'] ?? null;

        $grades = Grade::query()->whereIn('id', $gradeIds)->get()->keyBy('id');
        $sections = \App\Models\Section::query()->whereIn('id', $sectionIds)->get()->keyBy('id');
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
}
