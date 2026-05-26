<?php

namespace App\Filament\Admin\Resources\Classrooms\Schemas;

use App\Enums\GeneralStatus;
use App\Models\Classroom;
use App\Models\Role;
use App\Models\Staff;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                            TextInput::make('name')
                                ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                ->hintIconTooltip(__('This field is automatically generated based on the selected grade and section.'))
                                ->disabledOn(Operation::Create->value)
                                ->required(fn (string $operation) => $operation !== Operation::Create->value),
                            TextInput::make('capacity')
                                ->integer()
                                ->required(),
                            Select::make('grade_id')
                                ->label(__('Grade'))
                                ->relationship('grade', 'name')
                                ->preload()
                                ->searchable()
                                ->required(),
                            Select::make('section_id')
                                ->label(__('Section'))
                                ->relationship('section', 'name')
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
                                        ->whereHas('user', fn (Builder $q) => $q->where('role_id', Role::STAFF))
                                        ->with('user');
                                })
                                ->getOptionLabelFromRecordUsing(fn (Staff $record) => $record->user->name)
                                ->getOptionLabelUsing(fn (?Classroom $record, mixed $state) => $record?->formTeacher->user->name ?? $state)
                                ->preload()
                                ->searchable(),
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
}
