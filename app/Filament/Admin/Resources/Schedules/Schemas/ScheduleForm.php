<?php

namespace App\Filament\Admin\Resources\Schedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->required(),
                DateTimePicker::make('date')
                    ->required(),
                TimePicker::make('end_time')
                    ->required(),
                Select::make('exam_id')
                    ->relationship('exam', 'name')
                    ->required(),
                TextInput::make('full_mark')
                    ->required()
                    ->numeric(),
                TextInput::make('pass_mark')
                    ->required()
                    ->numeric(),
                TimePicker::make('start_time')
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }
}
