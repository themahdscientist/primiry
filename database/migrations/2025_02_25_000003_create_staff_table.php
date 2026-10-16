<?php

use App\Models\School;
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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->json('bank_account_details')->nullable(); // Ensure compliance with data protection laws
            $table->string('code')->unique();
            $table->timestamp('contract_expiry_date', 6)->nullable();
            $table->string('emergency_contact')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contractual'])->default('full-time');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->text('notes')->nullable();
            $table->json('qualifications')->nullable(); // array of 2 credentials max
            $table->decimal('salary', 10, 2)->nullable(); // max = 99,999,999.99

            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('staff');
    }
};
