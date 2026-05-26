<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Enums\Sex;
use App\Enums\StudentStatus;
use App\Enums\StudentType;
use App\Filament\Admin\Resources\Guardians\GuardianResource;
use App\Models\User;
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

class StudentsTable
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
                TextColumn::make('student.admission_number')
                    ->label('Admission number')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                TextColumn::make('student.dob')
                    ->label('DOB')
                    ->sortable()
                    ->date(),
                TextColumn::make('sex'),
                TextColumn::make('student.classroom.name')
                    ->searchable(),
                TextColumn::make('student.guardian.user.name')
                    ->label('Guardian')
                    ->searchable()
                    ->url(fn (User $record) => $record->student->guardian->user ? GuardianResource::getUrl('view', [$record->student->guardian->user]) : null)
                    ->emptyTooltip('A guardian  hasn\'t been assigned yet.')
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nin')
                    ->label('NIN')
                    ->placeholder('-')
                    ->emptyTooltip('Click to link NIN.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                PhoneColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.status')
                    ->label('Status')
                    ->searchable(),
                TextColumn::make('student.type')
                    ->label('Type'),
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
