<?php

use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\School;
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
        Schema::create('staff_staff_role', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Staff::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(StaffRole::class)->nullable()->index();
            $table->foreignIdFor(School::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_staff_role');
    }
};
