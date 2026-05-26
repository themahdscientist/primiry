<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function count;

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
        $data['staff'] = $this->getRecord()->staff->attributesToArray();

        $nameParts = explode(' ', $data['name'] ?? '');
        $data['first_name'] = $nameParts[0] ?? null;
        $data['last_name'] = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : null;

        return $data = Arr::except($data, 'name');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = $data['first_name'].' '.$data['last_name'];

        return $data = Arr::except($data, ['first_name', 'last_name']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $record = parent::handleRecordUpdate($record, Arr::except($data, 'staff'));
    }

    protected function afterSave(): void
    {
        $this->getRecord()->staff()->update($this->form->getState()['staff']);
    }
}
