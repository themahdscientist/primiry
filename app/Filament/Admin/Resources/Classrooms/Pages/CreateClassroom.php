<?php

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassroom extends CreateRecord
{
    protected static string $resource = ClassroomResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $grades = $this->form->getComponentByStatePath('grade_id')->getOptions();
        $sections = $this->form->getComponentByStatePath('section_id')->getOptions();

        $data['name'] = "{$grades[$data['grade_id']]} {$sections[$data['section_id']]}";

        return $data;
    }
}
