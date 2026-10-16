<?php

namespace App\Filament\Admin\Resources\Guardians;

use App\Filament\Admin\Resources\Guardians\Pages\CreateGuardian;
use App\Filament\Admin\Resources\Guardians\Pages\EditGuardian;
use App\Filament\Admin\Resources\Guardians\Pages\ListGuardians;
use App\Filament\Admin\Resources\Guardians\Pages\ViewGuardian;
use App\Filament\Admin\Resources\Guardians\Schemas\GuardianForm;
use App\Filament\Admin\Resources\Guardians\Tables\GuardiansTable;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::UserCircle;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->user?->getAttribute(static::getRecordTitleAttribute()) ?? static::getModelLabel();
    }

    public static function form(Schema $schema): Schema
    {
        return GuardianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuardiansTable::configure($table);
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
            'index' => ListGuardians::route('/'),
            'create' => CreateGuardian::route('/create'),
            'edit' => EditGuardian::route('/{record}/edit'),
            'view' => ViewGuardian::route('/{record}'),
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
