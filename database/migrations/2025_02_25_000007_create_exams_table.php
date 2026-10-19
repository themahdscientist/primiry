<?php

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Grade::class)->constrained()->cascadeOnDelete();
            $table->string('session');
            $table->string('semester');
            $table->foreignIdFor(Subject::class)->constrained()->cascadeOnDelete();
            $table->timestamp('start_date', 6);
            $table->timestamp('end_date', 6);
            $table->time('start_time', 6);
            $table->time('end_time', 6);
            $table->integer('full_mark');
            $table->integer('pass_mark');
            
            $table->foreignIdFor(School::class)->constrained()->cascadeOnDelete();
            $table->timestamps(6);
            $table->softDeletes(precision: 6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
