<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

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

        $nameParts = preg_split('/\s+/', trim($data['user']['name'] ?? ''), 2) ?: [];
        $data['user']['first_name'] = $nameParts[0] ?? null;
        $data['user']['last_name'] = $nameParts[1] ?? null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user']['name'] = trim(implode(' ', [
            $data['user']['first_name'],
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
