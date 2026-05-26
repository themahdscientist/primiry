<?php

use App\Models\Country;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->text('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('bvn')->nullable();
            $table->foreignIdFor(Country::class, 'billable_country_code')->index()->nullable();
            $table->enum('kyc', ['pending', 'verified', 'failed'])->default('pending');

            $table->string('sch_address');
            $table->json('sch_affiliates')->nullable(); // jamb;waec;neco;gce
            $table->json('sch_aliases')->nullable();
            $table->string('sch_code')->unique()->nullable();
            $table->json('sch_contact')->nullable(); // email;website
            $table->foreignIdFor(Country::class, 'sch_country')->index();
            $table->timestamp('sch_est', 6)->nullable();
            $table->string('sch_logo')->nullable();
            $table->text('sch_mission')->nullable();
            $table->text('sch_name');
            $table->string('sch_period')->nullable();
            $table->string('sch_pmb')->nullable();
            $table->string('sch_session')->nullable();
            $table->foreignIdFor(State::class, 'sch_state')->index();
            $table->text('sch_vision')->nullable();

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
        Schema::dropIfExists('settings');
    }
};
