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
        $record = parent::handleRecordCreation(Arr::except($data, 'user'));

        Role::find(Role::STUDENT)->users()->create($data['user'])->student()->save($record);

        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user']['name'] = trim(implode(' ', [
            $data['user']['first_name'],
            $data['user']['middle_name'],
            $data['user']['last_name'],
        ]));

        $data['user'] = Arr::except($data['user'], [
            'first_name',
            'middle_name',
            'last_name',
        ]);

        return $data;
    }
}
