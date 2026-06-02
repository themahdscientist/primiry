<?php

use App\Models\Classroom;
use App\Models\School;
use App\Models\Staff;
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
        Schema::create('classroom_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Classroom::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Staff::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(School::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_staff');
    }
};
