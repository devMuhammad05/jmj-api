<?php

use App\Enums\PoolInvestmentStatus;
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
        Schema::create('pool_investments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('pool_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->decimal('contribution', 15, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('share_percentage', 8, 4)->default(0);
            $table->string('status')->default(PoolInvestmentStatus::PENDING->value);
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'pool_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pool_investments');
    }
};
