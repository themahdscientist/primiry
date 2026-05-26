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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['grade', 'section', 'formTeacher.user']);
    }
}
