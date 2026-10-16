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
        $record = parent::handleRecordCreation(Arr::except($data, 'user'));

        Role::find(Role::GUARDIAN)->users()->create($data['user'])->guardian()->save($record);

        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user']['name'] = trim(implode(' ', [
            $data['user']['first_name'],
            $data['user']['last_name'],
        ]));

        $data['user'] = Arr::except($data['user'], [
            'first_name',
            'last_name',
        ]);

        return $data;
    }
}
