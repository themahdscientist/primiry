<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Filament\Admin\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function count;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user'] = $this->getRecord()->user->attributesToArray();

        $nameParts = preg_split('/\s+/', trim($data['user']['name'] ?? ''), 3) ?: [];
        $data['user']['first_name'] = $nameParts[0] ?? null;
        $data['user']['middle_name'] = $nameParts[1] ?? null;
        $data['user']['last_name'] = $nameParts[2] ?? null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user']['name'] = trim(implode(' ', [
            $data['user']['first_name'],
            $data['user']['middle_name'],
            $data['user']['last_name'],
        ]));

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->user->update($data['user']);

        return parent::handleRecordUpdate($record, Arr::except($data, 'user'));
    }
}
