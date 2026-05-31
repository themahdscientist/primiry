<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tables to add school_id to
        $tables = [
            'classrooms','grades','sections','students','staff','guardians',
            'subjects','timetables','schedules','results','subscriptions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->foreignId('school_id')->nullable()->constrained('schools')->cascadeOnDelete()->index();
                });
            }
        }

        // Pivot tables
        $pivots = ['classroom_staff','classroom_subject','grade_subject','staff_staff_role'];
        foreach ($pivots as $pivot) {
            if (Schema::hasTable($pivot) && ! Schema::hasColumn($pivot, 'school_id')) {
                Schema::table($pivot, function (Blueprint $t) use ($pivot) {
                    $t->foreignId('school_id')->nullable()->constrained('schools')->cascadeOnDelete()->index();
                });
            }
        }

        // Adjust unique constraints to be scoped by school_id
        // students.admission_number
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $t) {
                if (Schema::hasColumn('students', 'admission_number')) {
                    try {
                        $t->dropUnique(['admission_number']);
                    } catch (\Throwable $e) {
                        // ignore if index not exists
                    }
                }
            });
            Schema::table('students', function (Blueprint $t) {
                if (Schema::hasColumn('students', 'school_id') && Schema::hasColumn('students', 'admission_number')) {
                    $t->unique(['school_id', 'admission_number']);
                }
            });
        }

        // sections.name
        if (Schema::hasTable('sections')) {
            Schema::table('sections', function (Blueprint $t) {
                try { $t->dropUnique(['name']); } catch (\Throwable $e) {}
            });
            Schema::table('sections', function (Blueprint $t) {
                if (Schema::hasColumn('sections','school_id')) $t->unique(['school_id','name']);
            });
        }

        // grades.name
        if (Schema::hasTable('grades')) {
            Schema::table('grades', function (Blueprint $t) {
                try { $t->dropUnique(['name']); } catch (\Throwable $e) {}
            });
            Schema::table('grades', function (Blueprint $t) {
                if (Schema::hasColumn('grades','school_id')) $t->unique(['school_id','name']);
            });
        }

        // guardians.code
        if (Schema::hasTable('guardians')) {
            Schema::table('guardians', function (Blueprint $t) {
                try { $t->dropUnique(['code']); } catch (\Throwable $e) {}
            });
            Schema::table('guardians', function (Blueprint $t) {
                if (Schema::hasColumn('guardians','school_id')) $t->unique(['school_id','code']);
            });
        }

        // staff.code
        if (Schema::hasTable('staff')) {
            Schema::table('staff', function (Blueprint $t) {
                try { $t->dropUnique(['code']); } catch (\Throwable $e) {}
            });
            Schema::table('staff', function (Blueprint $t) {
                if (Schema::hasColumn('staff','school_id')) $t->unique(['school_id','code']);
            });
        }

        // subscriptions.subscription_code -> scope by school
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $t) {
                try { $t->dropUnique(['subscription_code']); } catch (\Throwable $e) {}
            });
            Schema::table('subscriptions', function (Blueprint $t) {
                if (Schema::hasColumn('subscriptions','school_id')) $t->unique(['school_id','subscription_code']);
            });
        }

        // Note: No automatic backfill performed here. Run manual backfill scripts to populate school_id from your user->school mapping.
    }

    public function down(): void
    {
        // Remove added columns and scoped uniques
        $tables = [
            'classrooms','grades','sections','students','staff','guardians',
            'subjects','timetables','schedules','results','subscriptions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    // drop scoped uniques if present
                    try {
                        if (Schema::hasColumn($table, 'admission_number')) $t->dropUnique(["{$table}_school_id_admission_number_unique"]);
                    } catch (\Throwable $e) {}
                    try {
                        if (Schema::hasColumn($table, 'name')) $t->dropUnique(["{$table}_school_id_name_unique"]);
                    } catch (\Throwable $e) {}

                    $t->dropConstrainedForeignId('school_id');
                });
            }
        }

        $pivots = ['classroom_staff','classroom_subject','grade_subject','staff_staff_role'];
        foreach ($pivots as $pivot) {
            if (Schema::hasTable($pivot) && Schema::hasColumn($pivot, 'school_id')) {
                Schema::table($pivot, function (Blueprint $t) {
                    $t->dropConstrainedForeignId('school_id');
                });
            }
        }
    }
};
