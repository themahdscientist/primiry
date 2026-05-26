<?php

use App\Models\Staff;
use App\Models\User;
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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->enum('age_range', [
                '5-6',
                '6-7',
                '7-8',
                '8-9',
                '9-10',
                '10-11',
                '11-12',
                '12-13',
                '13-14',
                '14-15',
                '15-16',
                '16-17',
                '17-18',
            ]);
            $table->json('assessment_methods');
            $table->text('description')->nullable();
            $table->timestamp('end_date', 6);
            $table->json('meeting_methods');
            $table->string('name')->unique();
            $table->foreignIdFor(Staff::class, 'year_head_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('start_date', 6);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps(6);
            $table->softDeletes(precision: 6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
