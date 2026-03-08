<?php

use App\Enums\ProfitDistributionStatus;
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
        Schema::create('profit_distributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pool_investment_id')->constrained()->cascadeOnDelete();
            $table->date('distribution_date');
            $table->decimal('profit_amount', 15, 2);
            $table->decimal('pool_return', 8, 2);
            $table->string('status')->default(ProfitDistributionStatus::PENDING->value);
            $table->timestamp('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index('pool_investment_id');
            $table->index('distribution_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_distributions');
    }
};
