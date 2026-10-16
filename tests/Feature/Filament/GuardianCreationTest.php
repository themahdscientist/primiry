<?php

use App\Filament\Admin\Resources\Guardians\GuardianResource;
use App\Filament\Admin\Resources\Guardians\Pages\CreateGuardian;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('guardian creation relies on filament tenancy to save the current school', function () {
    Filament::setCurrentPanel('admin');

    $school = School::create([
        'name' => 'Example School',
        'code' => 'example-school',
    ]);

    $admin = User::factory()->create([
        'phone' => '+15555550100',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    GuardianResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    DB::connection()->setQueryGrammar(new SQLiteGrammar(DB::connection()));

    $creator = new class extends CreateGuardian
    {
        public function createGuardian(array $data): Model
        {
            $data = $this->mutateFormDataBeforeCreate($data);

            return $this->handleRecordCreation($data);
        }
    };

    $guardian = $creator->createGuardian([
        'user' => [
            'first_name' => 'Taylor',
            'last_name' => 'Guardian',
            'email' => 'taylor@example.com',
            'phone' => '+15555550101',
            'sex' => 'female',
            'nin' => '12345678901',
        ],
        'code' => 'guardian001',
        'marital_status' => 'married',
        'occupation' => 'Teacher',
    ]);

    expect($guardian)
        ->toBeInstanceOf(Guardian::class)
        ->and($guardian->user)->toBeInstanceOf(User::class)
        ->and($guardian->user->name)->toBe('Taylor Guardian')
        ->and($guardian->user->email)->toBe('taylor@example.com')
        ->and($guardian->user->role_id)->toBe(Role::GUARDIAN)
        ->and($guardian->school_id)->toBe($school->getKey())
        ->and($guardian->marital_status)->toBe('married')
        ->and($guardian->occupation)->toBe('Teacher');
});

test('guardian creation rolls back the user when the guardian cannot be created', function () {
    Filament::setCurrentPanel('admin');

    $school = School::create([
        'name' => 'Example School',
        'code' => 'example-school',
    ]);

    $admin = User::factory()->create([
        'phone' => '+15555550100',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    GuardianResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    DB::connection()->setQueryGrammar(new SQLiteGrammar(DB::connection()));

    $existingGuardian = new Guardian([
        'code' => 'guardian001',
    ]);
    $existingGuardian->school()->associate($school);
    $existingGuardian->save();

    $creator = new class extends CreateGuardian
    {
        public function createGuardian(array $data): Model
        {
            $data = $this->mutateFormDataBeforeCreate($data);

            return $this->handleRecordCreation($data);
        }
    };

    expect(fn () => $creator->createGuardian([
        'user' => [
            'first_name' => 'Taylor',
            'last_name' => 'Guardian',
            'email' => 'rollback@example.com',
            'phone' => '+15555550101',
            'sex' => 'female',
            'nin' => '12345678901',
        ],
        'code' => 'guardian001',
        'marital_status' => 'married',
        'occupation' => 'Teacher',
    ]))->toThrow(QueryException::class);

    expect(User::query()->where('email', 'rollback@example.com')->exists())->toBeFalse();
});
