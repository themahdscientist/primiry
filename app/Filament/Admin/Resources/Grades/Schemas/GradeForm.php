<?php

namespace App\Filament\Admin\Resources\Grades\Schemas;

use App\Enums\AgeRange;
use App\Enums\AssessmentMethods;
use App\Enums\GeneralStatus;
use App\Enums\GradeName;
use App\Enums\MeetingMethods;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Staff;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Sidebar::make([
                    Section::make()
                        ->columns()
                        ->columnSpanFull()
                        ->schema([
                            Select::make('name')
                                ->options(GradeName::class)
                                ->required()
                                ->native(false)
                                ->unique(Grade::class, 'name')
                                ->afterStateUpdated(fn(mixed $state, Set $set): mixed => static::getGradeAgeRange($state, $set)),
                            Select::make('age_range')
                                ->options(AgeRange::class)
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->native(false),
                            Select::make('status')
                                ->options(GeneralStatus::class)
                                ->required()
                                ->default(GeneralStatus::Active)
                                ->native(false),
                            Select::make('year_head_id')
                                ->label(__('Year head'))
                                ->relationship('yearHead', 'id')
                                ->getOptionLabelFromRecordUsing(fn(Staff $record) => $record->user->name)
                                ->searchable()
                                ->preload(),
                            DatePicker::make('start_date')
                                ->label('Start date')
                                ->required()
                                ->native(false),
                            DatePicker::make('end_date')
                                ->label('End date')
                                ->required()
                                ->native(false),
                            Textarea::make('description')
                                ->rows(1)
                                ->columnSpanFull(),
                            Select::make('assessment_methods')
                                ->multiple()
                                ->options(AssessmentMethods::class)
                                ->required()
                                ->native(false),
                            Select::make('meeting_methods')
                                ->multiple()
                                ->options(MeetingMethods::class)
                                ->required()
                                ->native(false),
                            // this does not assign the tenant id to the pivot table. fix this.
                            CheckboxList::make('subjects')
                                ->relationship(titleAttribute: 'name')
                            ->searchable()
                                ->live(condition: false)
                                ->columns(3)
                            ->bulkToggleable()
                                ->columnSpanFull(),
                        ]),
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

    protected static function getGradeAgeRange(mixed $state, Set $set): null
    {
        switch ($state) {
            case GradeName::G1st->value:
                $set('age_range', AgeRange::G1st);
                break;
            case GradeName::G2nd->value:
                $set('age_range', AgeRange::G2nd);
                break;
            case GradeName::G3rd->value:
                $set('age_range', AgeRange::G3rd);
                break;
            case GradeName::G4th->value:
                $set('age_range', AgeRange::G4th);
                break;
            case GradeName::G5th->value:
                $set('age_range', AgeRange::G5th);
                break;
            case GradeName::G6th->value:
                $set('age_range', AgeRange::G6th);
                break;
            case GradeName::G7th->value:
                $set('age_range', AgeRange::G7th);
                break;
            case GradeName::G8th->value:
                $set('age_range', AgeRange::G8th);
                break;
            case GradeName::G9th->value:
                $set('age_range', AgeRange::G9th);
                break;
            case GradeName::G10th->value:
                $set('age_range', AgeRange::G10th);
                break;
            case GradeName::G11th->value:
                $set('age_range', AgeRange::G11th);
                break;
            case GradeName::G12th->value:
                $set('age_range', AgeRange::G12th);
                break;

            default:
                $set('age_range', AgeRange::Kindergarten);
                break;
        }

        return null;
    }
}
