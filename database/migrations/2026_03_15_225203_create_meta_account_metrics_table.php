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
        Schema::create('meta_account_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('account_id');   // MetaAPI UUID
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('equity', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('deposits', 15, 2)->default(0);
            $table->decimal('withdrawals', 15, 2)->default(0);
            $table->decimal('margin', 15, 2)->default(0);
            $table->decimal('free_margin', 15, 2)->default(0);
            $table->integer('trades')->default(0);
            $table->decimal('profit_factor', 15, 2)->nullable();
            $table->decimal('sharpe_ratio', 15, 2)->nullable();
            $table->decimal('won_trades_percent', 5, 2)->nullable();
            $table->decimal('lost_trades_percent', 5, 2)->nullable();
            $table->json('daily_growth')->nullable(); // store daily balance snapshots
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_account_metrics');
    }
};
