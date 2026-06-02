<?php

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Models\School;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Classroom::class)->constrained()->cascadeOnDelete();
            $table->timestamp('date', 6);
            $table->time('end_time', 6);
            $table->foreignIdFor(Exam::class)->constrained()->cascadeOnDelete();
            $table->integer('full_mark');
            $table->integer('pass_mark');
            $table->time('start_time', 6);
            $table->foreignIdFor(Subject::class)->constrained()->cascadeOnDelete();
            
            $table->foreignIdFor(School::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps(6);
            $table->softDeletes(precision: 6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
