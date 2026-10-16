<?php

namespace App\Filament\Admin\Resources\Classrooms\Schemas;

use App\Enums\GeneralStatus;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Section as SchoolSection;
use App\Models\Staff;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Database\Eloquent\Builder;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Sidebar::make([
                    Section::make()
                        ->columns()
                        ->schema([
                            Select::make('grade_id')
                                ->label(__('Grade'))
                                ->relationship('grade', 'name')
                                ->preload()
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function (Set $set): void {
                                    $set('section_id', null);
                                    $set('name', null);
                                    $set('subjects', null);
                                })
                                ->required(),
                            Select::make('section_id')
                                ->label(__('Section'))
                                ->relationship(
                                    'section',
                                    'name',
                                    function (Builder $query, Get $get, ?Classroom $record, string $operation): Builder {
                                        $gradeId = $get('grade_id');

                                        if (blank($gradeId)) {
                                            return $query->whereKey([]);
                                        }

                                        $status = $operation === Operation::Create->value
                                            ? GeneralStatus::Active->value
                                            : $record?->status;

                                        if ($status !== GeneralStatus::Active->value) {
                                            return $query;
                                        }

                                        return $query->whereDoesntHave(
                                            'classrooms',
                                            fn(Builder $classrooms): Builder => $classrooms
                                                ->where('grade_id', $gradeId)
                                                ->where('status', GeneralStatus::Active->value)
                                                ->when(
                                                    $record,
                                                    fn(Builder $classrooms): Builder => $classrooms
                                                        ->whereKeyNot($record->getKey()),
                                                ),
                                        );
                                    },
                                )
                                ->live()
                                ->afterStateUpdated(function (mixed $state, Get $get, Set $set): void {
                                    self::updateClassroomName($get, $set, $state);
                                })
                                ->disabled(fn(Get $get): bool => blank($get('grade_id')))
                                ->preload()
                                ->searchable()
                                ->scopedUnique(
                                    modifyQueryUsing: function (Builder $query, Get $get, ?Classroom $record, string $operation): Builder {
                                        $status = $operation === Operation::Create->value
                                            ? GeneralStatus::Active->value
                                            : $record?->status;

                                        return $query
                                            ->where('grade_id', $get('grade_id'))
                                            ->when(
                                                $status === GeneralStatus::Active->value,
                                                fn(Builder $query): Builder => $query
                                                    ->where('status', GeneralStatus::Active->value),
                                                fn(Builder $query): Builder => $query->whereKey([]),
                                            );
                                    },
                                )
                                ->validationMessages([
                                    'unique' => 'A classroom already exists for this grade and section.',
                                ])
                                ->required(),
                            TextInput::make('name')
                                ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                ->hintIconTooltip(__('This name is suggested based on the selected grade and section, but can be changed.'))
                                ->saved(),
                            TextInput::make('capacity')
                                ->integer()
                                ->required(),
                            Select::make('status')
                                ->options(GeneralStatus::class)
                                ->required()
                                ->default(GeneralStatus::Active)
                                ->disabled()
                                ->saved(),
                            Select::make('form_teacher_id')
                                ->label(__('Form teacher'))
                                ->relationship('formTeacher', 'id')
                                ->getOptionLabelFromRecordUsing(fn(Staff $record) => $record->user->name)
                                ->searchable()
                                ->preload(),
                            // this does not assign the tenant id to the pivot table. fix this.
                            CheckboxList::make('subjects')
                                ->helperText(__('The subjects are populated based on the selected grade.'))
                                ->relationship(titleAttribute: 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                    $query->whereHas('grades', function (Builder $q) use ($get) {
                                        $q->where('grades.id', $get('grade_id'));
                                    });
                                })
                            ->searchable()
                                ->live(condition: false)
                                ->columns(3)
                            ->bulkToggleable()
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ], [
                    Section::make([
                        CreatedAt::make('Created'),
                        DeletedAt::make('Deleted'),
                    ])
                        ->columnSpanFull(),
                ])
                    ->columnSpanFull(),
            ]);
    }

    private static function updateClassroomName(Get $get, Set $set, mixed $sectionId): void
    {
        $gradeId = $get('grade_id');

        if (blank($gradeId) || blank($sectionId)) {
            $set('name', null);

            return;
        }

        $grade = Grade::query()->find($gradeId);
        $section = SchoolSection::query()->find($sectionId);

        if (!$grade || !$section) {
            $set('name', null);

            return;
        }

        $set('name', trim("{$grade->name} {$section->name}"));
    }
}
