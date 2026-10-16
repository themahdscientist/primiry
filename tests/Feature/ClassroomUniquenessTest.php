<?php

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Filament\Admin\Resources\Classrooms\Pages\CreateClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\CreateMultipleClassrooms;
use App\Filament\Admin\Resources\Classrooms\Pages\EditClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\ListClassrooms;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Role;
use App\Models\School;
use App\Models\Section;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    DB::connection()->setQueryGrammar(new SQLiteGrammar(DB::connection()));
});

test('classroom creation rejects an existing grade and section pair within the active school', function () {
    [$school, $grade, $section] = createClassroomUniquenessFixture();
    $availableSection = new Section([
        'name' => 'B',
        'status' => 'active',
    ]);
    $availableSection->school()->associate($school);
    $availableSection->save();

    $otherGrade = new Grade([
        'age_range' => '13-14',
        'assessment_methods' => [],
        'end_date' => now()->addYear(),
        'meeting_methods' => [],
        'name' => 'Grade 8',
        'start_date' => now(),
        'status' => 'active',
    ]);
    $otherGrade->school()->associate($school);
    $otherGrade->save();

    $admin = User::factory()->create([
        'phone' => '+15555550100',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    ClassroomResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    ClassroomResource::registerTenancyModelGlobalScope(Filament::getPanel('admin'));

    $editForm = Livewire::test(EditClassroom::class, [
        'record' => Classroom::query()->firstOrFail()->getKey(),
    ]);

    $editSectionOptions = $editForm->instance()->getSchemaComponent('form.section_id')->getOptionsForJs();

    expect(collect($editSectionOptions)->pluck('value')->all())
        ->toContain((string) $section->getKey());

    $form = Livewire::test(CreateClassroom::class)
        ->fillForm([
            'grade_id' => $grade->getKey(),
            'section_id' => $availableSection->getKey(),
            'capacity' => 30,
        ]);

    expect($form->instance()->getSchemaComponent('form.status')->isDisabled())->toBeTrue()
        ->and($form->instance()->getSchemaComponent('form.name')->isDisabled())->toBeFalse()
        ->and($form->instance()->getSchemaComponent('form.name')->isReadOnly())->toBeFalse()
        ->and($form->instance()->getSchemaComponent('form.section_id')->isDisabled())->toBeFalse();

    $form->set('data.section_id', null)
        ->set('data.section_id', $availableSection->getKey());

    $form->instance()->getSchemaComponent('form.section_id')->callAfterStateUpdated();

    $form->assertSet('data.name', 'Grade 7 B');

    $sectionOptions = $form->instance()->getSchemaComponent('form.section_id')->getOptionsForJs();

    expect(collect($sectionOptions)->pluck('value')->all())
        ->toContain((string) $availableSection->getKey())
        ->not->toContain((string) $section->getKey());

    $form->set('data.status', 'inactive');

    $sectionOptionsAfterAttemptedStatusChange = $form->instance()->getSchemaComponent('form.section_id')->getOptionsForJs();

    expect(collect($sectionOptionsAfterAttemptedStatusChange)->pluck('value')->all())
        ->not->toContain((string) $section->getKey());

    $form->set('data.grade_id', $otherGrade->getKey())
        ->assertSet('data.section_id', null)
        ->assertSet('data.name', null);

    $updatedSectionOptions = $form->instance()->getSchemaComponent('form.section_id')->getOptionsForJs();

    expect(collect($updatedSectionOptions)->pluck('value')->all())
        ->toContain((string) $section->getKey());

    $form->set('data.section_id', $section->getKey())
        ->assertSet('data.name', 'Grade 8 A')
        ->fillForm([
            'grade_id' => $grade->getKey(),
            'section_id' => $section->getKey(),
            'capacity' => 30,
        ])
        ->call('create')
        ->tap(fn ($test) => expect($test->errors()->get('data.section_id'))
            ->toContain('A classroom already exists for this grade and section.'))
        ->assertHasFormErrors(['section_id']);

    expect(Classroom::query()->count())->toBe(1);

    $editForm->set('data.section_id', $availableSection->getKey())
        ->assertSet('data.name', 'Grade 7 B')
        ->set('data.name', 'Custom classroom name')
        ->call('save');

    expect(Classroom::query()->firstOrFail()->name)->toBe('Custom classroom name');
});

test('classroom name is suggested on create and remains editable on create and edit', function () {
    [$school, $grade] = createClassroomUniquenessFixture();
    $section = new Section([
        'name' => 'B',
        'status' => 'active',
    ]);
    $section->school()->associate($school);
    $section->save();

    $admin = User::factory()->create([
        'phone' => '+15555550104',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    ClassroomResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    ClassroomResource::registerTenancyModelGlobalScope(Filament::getPanel('admin'));

    $createForm = Livewire::test(CreateClassroom::class)
        ->fillForm([
            'grade_id' => $grade->getKey(),
            'section_id' => $section->getKey(),
            'capacity' => 30,
        ]);

    expect($createForm->instance()->getSchemaComponent('form.name')->isDisabled())->toBeFalse()
        ->and($createForm->instance()->getSchemaComponent('form.name')->isReadOnly())->toBeFalse();

    $createForm->assertSet('data.name', 'Grade 7 B')
        ->set('data.name', 'Custom classroom name')
        ->call('create')
        ->assertHasNoFormErrors();

    $classroom = Classroom::query()->where('section_id', $section->getKey())->firstOrFail();

    expect($classroom->name)->toBe('Custom classroom name');

    $editForm = Livewire::test(EditClassroom::class, ['record' => $classroom->getKey()]);

    expect($editForm->instance()->getSchemaComponent('form.name')->isDisabled())->toBeFalse()
        ->and($editForm->instance()->getSchemaComponent('form.name')->isReadOnly())->toBeFalse();

    $editForm->assertSet('data.name', 'Custom classroom name')
        ->set('data.name', 'Renamed classroom')
        ->call('save')
        ->assertHasNoFormErrors();

    expect($classroom->fresh()->name)->toBe('Renamed classroom');
});

test('bulk classroom creation rejects any selected grade and section pair that already exists', function () {
    [$school, $grade, $section] = createClassroomUniquenessFixture();
    $availableSection = new Section([
        'name' => 'B',
        'status' => 'active',
    ]);
    $availableSection->school()->associate($school);
    $availableSection->save();

    $otherGrade = new Grade([
        'age_range' => '13-14',
        'assessment_methods' => [],
        'end_date' => now()->addYear(),
        'meeting_methods' => [],
        'name' => 'Grade 8',
        'start_date' => now(),
        'status' => 'active',
    ]);
    $otherGrade->school()->associate($school);
    $otherGrade->save();
    $otherClassroom = new Classroom([
        'name' => 'Grade 8 B',
        'capacity' => 30,
        'status' => 'active',
    ]);
    $otherClassroom->grade()->associate($otherGrade);
    $otherClassroom->section()->associate($availableSection);
    $otherClassroom->school()->associate($school);
    $otherClassroom->save();

    $admin = User::factory()->create([
        'phone' => '+15555550101',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    ClassroomResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    ClassroomResource::registerTenancyModelGlobalScope(Filament::getPanel('admin'));

    $form = Livewire::test(CreateMultipleClassrooms::class)
        ->fillForm([
            'grades' => [$grade->getKey()],
            'sections' => [$section->getKey()],
            'capacity' => 30,
        ]);

    expect($form->instance()->getSchemaComponent('form.status')->isDisabled())->toBeTrue();

    $sectionOptions = $form->instance()->getSchemaComponent('form.sections')->getOptionsForJs();

    expect(collect($sectionOptions)->pluck('value')->all())
        ->toContain((string) $availableSection->getKey())
        ->not->toContain((string) $section->getKey());

    $form->set('data.grades', [$grade->getKey(), $otherGrade->getKey()]);

    $updatedSectionOptions = $form->instance()->getSchemaComponent('form.sections')->getOptionsForJs();

    expect(collect($updatedSectionOptions)->pluck('value')->all())
        ->not->toContain((string) $section->getKey())
        ->not->toContain((string) $availableSection->getKey());

    $form->fillForm([
        'grades' => [$grade->getKey()],
        'sections' => [$section->getKey()],
        'capacity' => 30,
    ])
        ->call('create')
        ->tap(fn ($test) => expect($test->errors()->get('data.grades'))
            ->toContain('One or more selected grade and section combinations already have a classroom.'))
        ->assertHasFormErrors(['grades']);

    expect(Classroom::query()->count())->toBe(2);
});

test('classroom status cannot be changed through create or edit form state', function () {
    [$school, $grade, $section] = createClassroomUniquenessFixture();
    $availableSection = new Section([
        'name' => 'B',
        'status' => 'active',
    ]);
    $availableSection->school()->associate($school);
    $availableSection->save();

    $admin = User::factory()->create([
        'phone' => '+15555550103',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    ClassroomResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    ClassroomResource::registerTenancyModelGlobalScope(Filament::getPanel('admin'));

    Livewire::test(CreateClassroom::class)
        ->fillForm([
            'grade_id' => $grade->getKey(),
            'section_id' => $availableSection->getKey(),
            'capacity' => 30,
            'status' => 'inactive',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $newClassroom = Classroom::query()->where('section_id', $availableSection->getKey())->firstOrFail();

    expect($newClassroom->status)->toBe('active');

    $newClassroom->status = 'inactive';
    $newClassroom->save();

    Livewire::test(EditClassroom::class, ['record' => $newClassroom->getKey()])
        ->fillForm(['status' => 'active'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($newClassroom->fresh()->status)->toBe('inactive');
});

test('database prevents duplicate active grade and section pairs', function () {
    [$school, $grade, $section] = createClassroomUniquenessFixture();

    $makeClassroom = fn (): Classroom => new Classroom([
        'name' => 'Duplicate classroom',
        'capacity' => 25,
        'status' => 'active',
    ]);
    $createClassroom = function () use ($makeClassroom, $school, $grade, $section): Classroom {
        $classroom = $makeClassroom();
        $classroom->school()->associate($school);
        $classroom->grade()->associate($grade);
        $classroom->section()->associate($section);
        $classroom->save();

        return $classroom;
    };
    $existingClassroom = Classroom::query()->firstOrFail();

    expect(fn () => $createClassroom())->toThrow(QueryException::class);

    $existingClassroom->delete();
    expect($existingClassroom->fresh()->status)->toBe('inactive');

    $replacementClassroom = $createClassroom();

    expect($replacementClassroom->status)->toBe('active');
    expect(fn () => $existingClassroom->restore())->toThrow(QueryException::class);

    $anotherInactiveClassroom = $makeClassroom();
    $anotherInactiveClassroom->status = 'inactive';
    $anotherInactiveClassroom->school()->associate($school);
    $anotherInactiveClassroom->grade()->associate($grade);
    $anotherInactiveClassroom->section()->associate($section);

    expect(fn () => $anotherInactiveClassroom->save())->toThrow(QueryException::class);
});

test('classroom restore reports a conflict instead of throwing when an active duplicate exists', function () {
    [$school, $grade, $section] = createClassroomUniquenessFixture();
    $trashedClassroom = Classroom::query()->firstOrFail();
    $trashedClassroom->delete();

    $activeClassroom = new Classroom([
        'name' => 'Replacement classroom',
        'capacity' => 30,
        'status' => 'active',
    ]);
    $activeClassroom->grade()->associate($grade);
    $activeClassroom->section()->associate($section);
    $activeClassroom->school()->associate($school);
    $activeClassroom->save();

    $admin = User::factory()->create([
        'phone' => '+15555550102',
        'sex' => 'female',
        'role_id' => Role::ADMIN,
    ]);
    $admin->schools()->attach($school);

    $this->actingAs($admin);
    Filament::setTenant($school, isQuiet: true);
    ClassroomResource::observeTenancyModelCreation(Filament::getPanel('admin'));
    ClassroomResource::registerTenancyModelGlobalScope(Filament::getPanel('admin'));

    Livewire::test(ListClassrooms::class)
        ->filterTable('trashed', false)
        ->callAction(TestAction::make('restore')->table($trashedClassroom))
        ->assertNotified('Classroom not restored')
        ->callTableBulkAction('restore', [$trashedClassroom])
        ->assertNotified('Classroom not restored');

    expect($trashedClassroom->fresh()->trashed())->toBeTrue()
        ->and($trashedClassroom->fresh()->status)->toBe('inactive')
        ->and($activeClassroom->fresh()->trashed())->toBeFalse();
});

test('restoring a classroom without a conflict sets its status to active', function () {
    createClassroomUniquenessFixture();

    $classroom = Classroom::query()->firstOrFail();
    $classroom->status = 'inactive';
    $classroom->save();
    $classroom->delete();

    expect($classroom->fresh()->status)->toBe('inactive')
        ->and($classroom->restore())->toBeTrue()
        ->and($classroom->fresh()->status)->toBe('active');
});

/**
 * @return array{School, Grade, Section}
 */
function createClassroomUniquenessFixture(): array
{
    $school = School::create([
        'name' => 'Example School',
        'code' => 'example-school-'.fake()->unique()->numerify('####'),
    ]);

    $grade = new Grade([
        'age_range' => '12-13',
        'assessment_methods' => [],
        'end_date' => now()->addYear(),
        'meeting_methods' => [],
        'name' => 'Grade 7',
        'start_date' => now(),
        'status' => 'active',
    ]);
    $grade->school()->associate($school);
    $grade->save();

    $section = new Section([
        'name' => 'A',
        'status' => 'active',
    ]);
    $section->school()->associate($school);
    $section->save();

    $classroom = new Classroom([
        'name' => 'Grade 7 A',
        'capacity' => 30,
        'status' => 'active',
    ]);
    $classroom->grade()->associate($grade);
    $classroom->section()->associate($section);
    $classroom->school()->associate($school);
    $classroom->save();

    return [$school, $grade, $section];
}
