<?php

use App\Filament\Admin\Resources\Guardians\Pages\EditGuardian;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('guardian edit updates guardian and related user while keeping unchanged unique fields', function () {
    Filament::setCurrentPanel('admin');

    $school = School::create([
        'name' => 'Example School',
        'code' => 'example-school',
        'settings' => [
            'address' => [
                'country_iso2' => 'US',
            ],
        ],
    ]);

    $admin = User::factory()->create([
        'phone' => '+15555550100',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $user = User::factory()->create([
        'name' => 'Taylor Guardian',
        'email' => 'taylor@example.com',
        'phone' => '+15555550101',
        'sex' => 'female',
        'nin' => '12345678901',
        'role_id' => Role::GUARDIAN,
    ]);

    $guardian = new Guardian([
        'code' => 'guardian001',
        'marital_status' => 'single',
        'occupation' => 'Teacher',
    ]);
    $guardian->school()->associate($school);
    $guardian->user()->associate($user);
    $guardian->save();

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    DB::connection()->setQueryGrammar(new SQLiteGrammar(DB::connection()));

    Livewire::test(EditGuardian::class, ['record' => $guardian->getKey()])
        ->fillForm([
            'user' => [
                'first_name' => 'Taylor',
                'last_name' => 'Updated',
                'nin' => '12345678901',
                'email' => 'taylor@example.com',
                'phone' => '+15555550102',
                'sex' => 'female',
            ],
            'marital_status' => 'married',
            'occupation' => 'Engineer',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($guardian->refresh()->occupation)->toBe('Engineer')
        ->and($guardian->user->refresh()->name)->toBe('Taylor Updated')
        ->and($guardian->user->email)->toBe('taylor@example.com')
        ->and($guardian->user->phone)->toBe('+15555550102')
        ->and($guardian->user->nin)->toBe('12345678901');
});
