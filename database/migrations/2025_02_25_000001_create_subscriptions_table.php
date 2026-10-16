<?php

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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('amount')->nullable();
            $table->timestamp('ends_at', 6)->nullable();
            $table->string('plan_code')->unique();
            $table->integer('quantity')->nullable();
            $table->string('status')->index();
            $table->timestamp('starts_at', 6)->nullable();
            $table->string('subscription_code');
            $table->timestamp('trial_ends_at', 6)->nullable();
            $table->string('type');

            $table->foreignIdFor(School::class)->constrained()->cascadeOnDelete();
            $table->unique(['school_id', 'subscription_code']);
            $table->timestamps(6);
            $table->softDeletes(precision: 6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
