<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Sushi\Sushi;

class State extends Model
{
    use Sushi;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $schema = [
        'id' => 'string',
        'name' => 'string',
        'state_code' => 'string',
        'type' => 'string',
        'country_iso2' => 'string',
    ];

    public function getRows()
    {
        $countries = json_decode(file_get_contents(storage_path('app/private/db/countries+states.json')), true);
        $states = [];

        foreach ($countries as $country) {
            if (! isset($country['states']) || ! is_array($country['states'])) {
                continue;
            }

            foreach ($country['states'] as $state) {
                $states[] = [
                    'id' => $state['id'] ?? null,
                    'name' => $state['name'] ?? null,
                    'state_code' => $state['state_code'] ?? null,
                    'type' => $state['type'] ?? null,
                    'country_iso2' => $country['iso2'] ?? null,
                ];
            }
        }

        return $states;
    }

    protected function afterMigrate(Blueprint $table)
    {
        $table->index(['name', 'state_code', 'country_iso2']);
    }

    protected function sushiShouldCache()
    {
        return true;
    }

    protected function sushiCacheReferencePath()
    {
        return storage_path('app/private/db/countries+states.json');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_iso2', 'iso2');
    }
}
