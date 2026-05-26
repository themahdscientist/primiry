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
        $data['student'] = $this->getRecord()->student->attributesToArray();

        $nameParts = explode(' ', $data['name'] ?? '');
        $data['first_name'] = $nameParts[0] ?? null;
        $data['middle_name'] = count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : null;
        $data['last_name'] = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : null;

        return $data = Arr::except($data, 'name');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $nameParts = array_filter([
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'] ?? null,
        ], fn ($v) => $v !== null && trim((string) $v) !== '');

        $data['name'] = implode(' ', $nameParts);

        return $data = Arr::except($data, ['first_name', 'middle_name', 'last_name']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $record = parent::handleRecordUpdate($record, Arr::except($data, 'student'));
    }

    protected function afterSave(): void
    {
        $this->getRecord()->student()->update($this->form->getState()['student']);
    }
}
