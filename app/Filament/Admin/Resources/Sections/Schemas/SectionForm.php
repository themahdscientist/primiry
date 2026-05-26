<?php

namespace App\Filament\Admin\Resources\Sections\Schemas;

use App\Enums\GeneralStatus;
use App\Enums\SectionName;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Sidebar::make([
                    Section::make()
                        ->columns(3)
                        ->schema([
                            Select::make('name')
                                ->options(SectionName::class)
                                ->required()
                                ->unique(\App\Models\Section::class, 'name')
                                ->native(false),
                            Textarea::make('description')
                                ->rows(1),
                            Select::make('status')
                                ->options(GeneralStatus::class)
                                ->required()
                                ->default(GeneralStatus::Active)
                                ->native(false),
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
