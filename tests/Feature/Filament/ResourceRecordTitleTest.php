<?php

use App\Filament\Admin\Resources\Guardians\GuardianResource;
use App\Filament\Admin\Resources\Staff\StaffResource;
use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Guardian;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;

test('guardian staff and student resource titles use the related user name', function () {
    $user = new User([
        'name' => 'Taylor Example',
    ]);

    $records = [
        [GuardianResource::class, (new Guardian)->setRelation('user', $user)],
        [StaffResource::class, (new Staff)->setRelation('user', $user)],
        [StudentResource::class, (new Student)->setRelation('user', $user)],
    ];

    foreach ($records as [$resource, $record]) {
        expect($resource::getRecordTitleAttribute())->toBe('user.name')
            ->and($resource::getRecordTitle($record))->toBe('Taylor Example');
    }
});
