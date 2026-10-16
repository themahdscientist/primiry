<?php

namespace App\Filament\Admin\Resources\Classrooms\Tables;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Models\Classroom;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
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
                TextColumn::make('subjects_count')
                ->label('Subjects')
                    ->counts('subjects'),
                IconColumn::make('status')
                    ->icon(fn(Classroom $record): string => GeneralStatus::from($record->status)->getIcon())
                    ->color(fn(Classroom $record): string => GeneralStatus::from($record->status)->getColor())
                    ->tooltip(fn(Classroom $record): string => GeneralStatus::from($record->status)->getLabel()),
            ])
            ->filters([
                TrashedFilter::make()
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                ClassroomResource::restoreAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make()
                        ->before(function (RestoreBulkAction $action): void {
                            ClassroomResource::restoreBulkActionBefore($action);
                        }),
                ]),
            ]);
    }
}
