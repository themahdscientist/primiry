<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation(Arr::except($data, 'student'));

        $record->role()->associate(Role::STUDENT)->save();

        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $parts = array_filter([
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'] ?? null,
        ], fn ($v) => $v !== null && trim((string) $v) !== '');

        $data['name'] = implode(' ', $parts);

        $data = Arr::except($data, ['first_name', 'middle_name', 'last_name']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->getRecord()->student()->create($this->form->getState()['student']);
    }
}
