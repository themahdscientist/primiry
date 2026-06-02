<?php

use App\Models\Classroom;
use App\Models\Country;
use App\Models\Guardian;
use App\Models\School;
use App\Models\State;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number');
            $table->enum('blood_group', ['a', 'b', 'ab', 'o']);
            $table->string('city_of_residence');
            $table->foreignIdFor(Classroom::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Country::class, 'country_of_residence')->index();
            $table->timestamp('dob', 6);
            $table->string('emergency_contact')->nullable();
            $table->foreignIdFor(Guardian::class)->nullable()->constrained()->nullOnDelete();
            $table->string('lga_of_origin');
            $table->foreignIdFor(Country::class, 'nationality')->index();
            $table->enum('religion', ['christianity', 'islam', 'other']);
            $table->enum('rhesus_factor', ['rh+', 'rh-']);
            $table->foreignIdFor(State::class, 'state_of_residence')->index();
            $table->foreignIdFor(State::class, 'state_of_origin')->index();
            $table->enum('status', ['active', 'suspended', 'expelled', 'graduated'])->default('active');
            $table->enum('type', ['boarding', 'day']);

            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(School::class)->nullable()->constrained()->cascadeOnDelete();
            $table->unique(['school_id', 'admission_number']);
            $table->timestamp('graduated_at', 6)->nullable();
            $table->timestamps(6);
            $table->softDeletes(precision: 6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
