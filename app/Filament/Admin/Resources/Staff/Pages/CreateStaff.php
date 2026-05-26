<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation(Arr::except($data, 'staff'));

        $record->role()->associate(Role::STAFF)->save();

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
        $this->getRecord()->staff()->create($this->form->getState()['staff']);
    }
}
