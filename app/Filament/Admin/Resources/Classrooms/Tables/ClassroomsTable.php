<?php

namespace App\Filament\Admin\Resources\Classrooms\Tables;

use App\Enums\GeneralStatus;
use App\Models\Classroom;
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

class ClassroomsTable
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
                TextColumn::make('grade.name')
                    ->searchable(),
                TextColumn::make('section.name')
                    ->searchable(),
                TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('formTeacher.user.name')
                    ->label('Form teacher')
                    ->searchable()
                    ->placeholder('-')
                    ->emptyTooltip('A form teacher hasn\'t been assigned yet.'),
                IconColumn::make('status')
                    ->icon(fn (Classroom $record): string => GeneralStatus::from($record->status)->getIcon())
                    ->color(fn (Classroom $record): string => GeneralStatus::from($record->status)->getColor())
                    ->tooltip(fn (Classroom $record): string => GeneralStatus::from($record->status)->getLabel()),
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
