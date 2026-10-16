<?php

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Result;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

test('models with deleted at columns support soft delete queries', function () {
    $models = [
        Classroom::class,
        Exam::class,
        Grade::class,
        Guardian::class,
        Result::class,
        Schedule::class,
        School::class,
        Section::class,
        Staff::class,
        Student::class,
        Subject::class,
        Subscription::class,
        Timetable::class,
        User::class,
    ];

    foreach ($models as $model) {
        expect($model::query()->withTrashed())->toBeInstanceOf(Builder::class)
            ->and($model::query()->withoutTrashed()->toSql())->toContain('deleted_at')
            ->and($model::query()->onlyTrashed()->toSql())->toContain('deleted_at');
    }
});
