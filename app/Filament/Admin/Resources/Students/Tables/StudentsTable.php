<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Enums\Sex;
use App\Enums\StudentStatus;
use App\Enums\StudentType;
use App\Filament\Admin\Resources\Guardians\GuardianResource;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('')
                    ->rowIndex(),
                ImageColumn::make('user.avatar')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('admission_number')
                    ->searchable()
                    ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                TextColumn::make('dob')
                    ->label('DOB')
                    ->sortable()
                    ->date(),
                TextColumn::make('user.sex')
                    ->label('Sex'),
                TextColumn::make('classroom.name')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type'),
                /**
                 * check the iconcolumn docs
                 * there are fluent ways to do this better
                 */
                IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn(string $state): string => StudentStatus::from($state)->getIcon())
                    ->color(fn(string $state): string => StudentStatus::from($state)->getColor())
                    ->tooltip(fn(string $state): string => StudentStatus::from($state)->getLabel())
                    ->sortable(),
                TextColumn::make('guardian.user.name')
                    ->label('Guardian')
                    ->searchable()
                    ->url(fn(Student $record) => $record->guardian ? GuardianResource::getUrl('view', [$record->guardian]) : null)
                    ->emptyTooltip('A guardian  hasn\'t been assigned yet.')
                    ->placeholder('-'),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.nin')
                    ->label('NIN')
                    ->placeholder('-')
                    ->emptyTooltip('Click to link NIN.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                PhoneColumn::make('user.phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->native(false),
                SelectFilter::make('sex')
                    ->options(Sex::class)
                    ->native(false),
                SelectFilter::make('status')
                    ->options(StudentStatus::class)
                    ->native(false),
                SelectFilter::make('type')
                    ->options(StudentType::class)
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
