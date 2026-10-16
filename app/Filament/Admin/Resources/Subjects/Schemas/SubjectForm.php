<?php

namespace App\Filament\Admin\Resources\Subjects\Schemas;

use App\Enums\SubjectType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Str;
use Nette\Utils\Random;
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
                                ->required()
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('code', Str::slug($state ?? '') . Random::generate(3, '0-9'))),
                            TextInput::make('code')
                                ->dehydrateStateUsing(fn(string $state): string => strtolower($state))
                                ->formatStateUsing(fn(?string $state): string => strtoupper($state))
                                ->scopedUnique()
                                ->required(),
                            Select::make('type')
                                ->options(SubjectType::class)
                                ->default(SubjectType::Theory)
                                ->required()
                                ->native(false),
                            Textarea::make('description')
                                ->rows(1)
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
