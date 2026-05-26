<?php

namespace App\Filament\Admin\Resources\Grades\Tables;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Staff\StaffResource;
use App\Models\Grade;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('age_range')
                    ->badge(),
                TextColumn::make('yearHead.user.name')
                    ->label('Year head')
                    ->searchable()
                    ->placeholder('-')
                    ->emptyTooltip('A year head hasn\'t been assigned yet.')
                    ->url(fn (Grade $record) => $record->yearHead->user ? StaffResource::getUrl('view', [$record->yearHead->user]) : null),
                TextColumn::make('subjects_count')
                    ->label('No. of Subjects')
                    ->counts('subjects'),
                TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->icon(fn (Grade $record): string => GeneralStatus::from($record->status)->getIcon())
                    ->color(fn (Grade $record): string => GeneralStatus::from($record->status)->getColor())
                    ->tooltip(fn (Grade $record): string => GeneralStatus::from($record->status)->getLabel()),
            ])
            ->filters([
                TrashedFilter::make()
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
