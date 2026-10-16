<?php

use App\Models\Classroom;
use App\Models\Country;
use App\Models\Day;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Result;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Section;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\State;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

test('all application models use explicit mass assignment allowlists', function () {
    $fillableAttributesByModel = [
        Classroom::class => ['name', 'capacity', 'form_teacher_id', 'grade_id', 'section_id', 'status'],
        Country::class => [],
        Day::class => [],
        Exam::class => ['name', 'notes'],
        Grade::class => ['name', 'age_range', 'assessment_methods', 'description', 'end_date', 'meeting_methods', 'year_head_id', 'start_date', 'status'],
        Guardian::class => ['code', 'marital_status', 'occupation'],
        Result::class => [],
        Role::class => [],
        Schedule::class => ['classroom_id', 'date', 'end_time', 'exam_id', 'full_mark', 'pass_mark', 'start_time', 'subject_id'],
        School::class => ['name', 'code', 'settings', 'status'],
        Section::class => ['name', 'description', 'status'],
        Staff::class => ['bank_account_details', 'code', 'contract_expiry_date', 'emergency_contact', 'employment_type', 'marital_status', 'notes', 'qualifications', 'salary'],
        StaffRole::class => [],
        State::class => [],
        Student::class => ['admission_number', 'blood_group', 'city_of_residence', 'classroom_id', 'country_of_residence', 'dob', 'emergency_contact', 'guardian_id', 'lga_of_origin', 'nationality', 'religion', 'rhesus_factor', 'state_of_residence', 'state_of_origin', 'status', 'type', 'graduated_at'],
        Subject::class => ['name', 'description', 'type'],
        Subscription::class => ['amount', 'ends_at', 'plan_code', 'quantity', 'status', 'starts_at', 'subscription_code', 'trial_ends_at', 'type'],
        Timetable::class => ['day_id', 'classroom_id', 'subject_id', 'start_time', 'end_time'],
        User::class => ['name', 'email', 'phone', 'password', 'sex', 'nin', 'avatar', 'original_avatar'],
    ];

    expect(Model::isUnguarded())->toBeFalse();

    foreach ($fillableAttributesByModel as $modelClass => $fillableAttributes) {
        expect((new $modelClass)->getFillable())->toBe($fillableAttributes);
    }
});

test('privileged ownership and role fields are not mass assignable', function () {
    expect((new User)->isFillable('role_id'))->toBeFalse()
        ->and((new Guardian)->isFillable('user_id'))->toBeFalse()
        ->and((new Guardian)->isFillable('school_id'))->toBeFalse()
        ->and((new Student)->isFillable('user_id'))->toBeFalse()
        ->and((new Student)->isFillable('school_id'))->toBeFalse()
        ->and((new Staff)->isFillable('user_id'))->toBeFalse()
        ->and((new Staff)->isFillable('school_id'))->toBeFalse();
});
