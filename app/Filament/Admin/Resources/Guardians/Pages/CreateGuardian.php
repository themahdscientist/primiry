<?php

namespace App\Filament\Admin\Resources\Guardians\Pages;

use App\Filament\Admin\Resources\Guardians\GuardianResource;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateGuardian extends CreateRecord
{
    protected static string $resource = GuardianResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation(Arr::except($data, 'guardian'));

        $record->role()->associate(Role::GUARDIAN)->save();

        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = implode(' ', [$data['first_name'], $data['last_name']]);

        $data = Arr::except($data, ['first_name', 'last_name']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->getRecord()->guardian()->create($this->form->getState()['guardian']);
    }
}
