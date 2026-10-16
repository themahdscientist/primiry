<?php

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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // business identifier used in URLs lookups, and tenant resolution

            /*
             * settings (JSON): store non-relational, infrequently queried data to keep table compact.
             * Suggested keys to store here (examples):
             *  - contact: { email, phone, website }
             *  - address: { line1, city, state_code, country_iso2 }
             *  - branding: { logo_path, favicon }
             *  - academic: { default_session, default_semester }
             *  - billing_meta: { trial_ends_at, customer_code, plan_code }
             * Billing ledger/history should remain in subscriptions table (school_id FK).
             */
            $table->json('settings')->nullable();
            $table->json('auth')->nullable();
            $table->enum('status', ['active', 'suspended', 'closed'])->default('active');

            $table->timestamps(6);
            $table->softDeletes(precision: 6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
