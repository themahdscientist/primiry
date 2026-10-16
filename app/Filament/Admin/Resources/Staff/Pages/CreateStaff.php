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
        $record = parent::handleRecordCreation(Arr::except($data, 'user'));

        Role::find(Role::STAFF)->users()->create($data['user'])->staff()->save($record);

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
