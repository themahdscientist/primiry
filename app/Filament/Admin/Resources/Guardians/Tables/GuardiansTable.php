<?php

namespace App\Filament\Admin\Resources\Guardians\Tables;

use App\Enums\Sex;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class GuardiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('')
                    ->rowIndex(),
                ImageColumn::make('avatar')
                    ->label('Profile picture')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guardian.code')
                    ->label('ID')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('sex'),
                PhoneColumn::make('phone')
                    ->searchable(),
                TextColumn::make('nin')
                    ->label('NIN')
                    ->searchable(),
                TextColumn::make('guardian.occupation')
                    ->label('Occupation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->native(false),
                SelectFilter::make('sex')
                    ->options(Sex::class)
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
