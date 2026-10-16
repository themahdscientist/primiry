<?php

use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Tenancy\RegisterSchool;
use App\Models\Country;
use App\Models\Role;
use App\Models\School;
use App\Models\State;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    DB::connection()->setQueryGrammar(new SQLiteGrammar(DB::connection()));
});

test('new panel registrations receive admin access', function () {
    $registration = new class extends Register
    {
        public function registerUser(array $data): Model
        {
            return $this->handleRegistration($data);
        }
    };

    $user = $registration->registerUser([
        'name' => 'School Admin',
        'email' => 'admin@example.com',
        'phone' => '+15555550100',
        'sex' => 'female',
        'password' => 'password',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->canAccessPanel(Filament::getPanel('admin')))->toBeTrue();
});

test('school registration creates a tenant membership for the current admin', function () {
    $user = User::factory()->create([
        'phone' => '+15555550101',
        'sex' => 'male',
        'role_id' => Role::ADMIN,
    ]);

    $this->actingAs($user);

    $registration = new class extends RegisterSchool
    {
        public function registerSchool(array $data): Model
        {
            return $this->handleRegistration($data);
        }
    };

    $state = State::query()->firstOrFail();
    $country = Country::query()
        ->where('iso2', $state->country_iso2)
        ->firstOrFail();

    $school = $registration->registerSchool([
        'name' => 'Example School',
        'code' => 'example-school',
        'settings' => [
            'contact' => [
                'email' => 'office@example-school.test',
                'phone' => '+15555550103',
                'website' => 'https://example-school.test',
            ],
            'address' => [
                'line1' => '1 School Road',
                'city' => 'Example City',
                'state_code' => $state->state_code,
                'country_iso2' => $country->iso2,
            ],
            'academic' => [
                'default_session' => now()->year.'/'.(now()->year + 1),
                'default_semester' => 'First semester',
            ],
        ],
    ]);

    expect($school)
        ->toBeInstanceOf(School::class)
        ->and($school->fresh()->settings)->toBe([
            'contact' => [
                'email' => 'office@example-school.test',
                'phone' => '+15555550103',
                'website' => 'https://example-school.test',
            ],
            'address' => [
                'line1' => '1 School Road',
                'city' => 'Example City',
                'state_code' => $state->state_code,
                'country_iso2' => $country->iso2,
            ],
            'academic' => [
                'default_session' => now()->year.'/'.(now()->year + 1),
                'default_semester' => 'First semester',
            ],
        ])
        ->and($school->status)->toBe('active')
        ->and($school->auth)->toBeNull()
        ->and($user->fresh()->canAccessTenant($school))->toBeTrue()
        ->and(RegisterSchool::canView())->toBeTrue();
});

test('school registration is unavailable to non-admin users', function () {
    $user = User::factory()->create([
        'phone' => '+15555550102',
        'sex' => 'male',
        'role_id' => Role::STUDENT,
    ]);

    $this->actingAs($user);

    expect(RegisterSchool::canView())->toBeFalse();
});
