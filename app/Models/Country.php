<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Sushi\Sushi;

#[Fillable([])]
class Country extends Model
{
    use Sushi;

    protected $keyType = 'string';

    protected $schema = [
        'name' => 'string',
        'iso2' => 'string',
        'iso3' => 'string',
        'phone_code' => 'string',
        'capital' => 'string',
        'currency' => 'string',
        'currency_symbol' => 'string',
        'region' => 'string',
        'emoji' => 'string',
    ];

    public function getRows()
    {
        $countries = json_decode(file_get_contents(storage_path('app/private/db/countries+states.json')), true);

        return array_map(function ($country) {
            return [
                'name' => $country['name'] ?? null,
                'iso2' => $country['iso2'] ?? null,
                'iso3' => $country['iso3'] ?? null,
                'phone_code' => $country['phonecode'] ?? null,
                'capital' => $country['capital'] ?? null,
                'currency' => $country['currency'] ?? null,
                'currency_symbol' => $country['currency_symbol'] ?? null,
                'region' => $country['region'] ?? null,
                'emoji' => $country['emoji'] ?? null,
            ];
        }, $countries);
    }

    protected function afterMigrate(Blueprint $table)
    {
        $table->index(['name', 'iso2', 'iso3']);
    }

    protected function sushiShouldCache()
    {
        return true;
    }

    protected function sushiCacheReferencePath()
    {
        return storage_path('app/private/db/countries+states.json');
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_iso2', 'iso2');
    }
}
