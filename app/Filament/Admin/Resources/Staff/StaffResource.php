<?php

namespace App\Filament\Admin\Resources\Staff;

use App\Filament\Admin\Resources\Staff\Pages\CreateStaff;
use App\Filament\Admin\Resources\Staff\Pages\EditStaff;
use App\Filament\Admin\Resources\Staff\Pages\ListStaff;
use App\Filament\Admin\Resources\Staff\Pages\ViewStaff;
use App\Filament\Admin\Resources\Staff\Schemas\StaffForm;
use App\Filament\Admin\Resources\Staff\Tables\StaffTable;
use App\Models\Staff;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Briefcase;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->user?->getAttribute(static::getRecordTitleAttribute()) ?? static::getModelLabel();
    }

    public static function form(Schema $schema): Schema
    {
        return StaffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffTable::configure($table);
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
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'edit' => EditStaff::route('/{record}/edit'),
            'view' => ViewStaff::route('/{record}'),
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
            ->with('user');
    }
}
