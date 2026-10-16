<?php

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassroom extends CreateRecord
{
    protected static string $resource = ClassroomResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = GeneralStatus::Active->value;

        return $data;
    }
}
