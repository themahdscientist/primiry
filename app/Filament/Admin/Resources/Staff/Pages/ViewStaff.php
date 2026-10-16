<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;

class ViewStaff extends ViewRecord
{
    protected static string $resource = StaffResource::class;

    protected static array $userEditableAttributes = [
        'name',
        'nin',
        'email',
        'phone',
        'sex',
        'avatar',
        'original_avatar',
    ];

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user'] = Arr::only(
            $this->getRecord()->user->attributesToArray(),
            static::$userEditableAttributes,
        );

        $nameParts = preg_split('/\s+/', trim($data['user']['name'] ?? ''), 2) ?: [];
        $data['user']['first_name'] = $nameParts[0] ?? null;
        $data['user']['last_name'] = $nameParts[1] ?? null;

        return $data;
    }
}
