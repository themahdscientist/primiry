<?php

namespace App\Filament\Admin\Resources\Subjects\Schemas;

use App\Enums\SubjectType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;

class SubjectForm
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
                                ->maxLength(255)
                                ->autofocus()
                                ->required(),
                            Select::make('type')
                                ->options(SubjectType::class)
                                ->required()
                                ->default(SubjectType::Theory)
                                ->native(false),
                            Textarea::make('description')
                                ->rows(1)
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
}
