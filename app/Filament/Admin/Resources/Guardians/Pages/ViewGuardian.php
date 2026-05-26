<?php

namespace App\Filament\Admin\Resources\Guardians\Pages;

use App\Filament\Admin\Resources\Guardians\GuardianResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;

use function count;

class ViewGuardian extends ViewRecord
{
    protected static string $resource = GuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['guardian'] = $this->getRecord()->guardian->attributesToArray();

        $nameParts = explode(' ', $data['name'] ?? '');
        $data['first_name'] = $nameParts[0] ?? null;
        $data['last_name'] = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : null;

        return $data = Arr::except($data, 'name');
    }
}
