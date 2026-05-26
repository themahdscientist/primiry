<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;

class ViewStaff extends ViewRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
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
}
