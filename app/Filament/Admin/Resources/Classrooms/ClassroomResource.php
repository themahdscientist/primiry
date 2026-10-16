<?php

namespace App\Filament\Admin\Resources\Classrooms;

use App\Filament\Admin\Resources\Classrooms\Pages\CreateClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\CreateMultipleClassrooms;
use App\Filament\Admin\Resources\Classrooms\Pages\EditClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\ListClassrooms;
use App\Filament\Admin\Resources\Classrooms\Schemas\ClassroomForm;
use App\Filament\Admin\Resources\Classrooms\Tables\ClassroomsTable;
use App\Models\Classroom;
use BackedEnum;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\UniqueConstraintViolationException;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::BuildingLibrary;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClassroomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassroomsTable::configure($table);
    }

    public static function restoreAction(): RestoreAction
    {
        return RestoreAction::make()
            ->using(function (Classroom $record): bool {
                if ($record->hasActiveClassroomConflict()) {
                    return false;
                }

                try {
                    return $record->restore();
                } catch (UniqueConstraintViolationException $exception) {
                    if (! $record->hasActiveClassroomConflict()) {
                        throw $exception;
                    }

                    return false;
                }
            })
            ->failureNotification(self::restoreConflictNotification());
    }

    public static function restoreConflictNotification(): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Classroom not restored')
            ->body('Restoring this record would create a duplicate active classroom for the same school, grade, and section. Delete the active duplicate or select only one trashed record for this combination.');
    }

    public static function restoreBulkActionBefore(RestoreBulkAction $action): void
    {
        $selectedPairs = [];

        foreach ($action->getSelectedRecords() as $record) {
            if (! $record instanceof Classroom) {
                continue;
            }

            $pair = implode(':', [
                $record->school_id ?? 'null',
                $record->grade_id,
                $record->section_id,
            ]);

            if (isset($selectedPairs[$pair]) || $record->hasActiveClassroomConflict()) {
                self::restoreConflictNotification()->send();
                $action->halt();
            }

            $selectedPairs[$pair] = true;
        }
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassrooms::route('/'),
            'create' => CreateClassroom::route('/create'),
            'edit' => EditClassroom::route('/{record}/edit'),
            'multiple' => CreateMultipleClassrooms::route('/create-multiple'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['grade', 'section', 'formTeacher.user']);
    }
}
