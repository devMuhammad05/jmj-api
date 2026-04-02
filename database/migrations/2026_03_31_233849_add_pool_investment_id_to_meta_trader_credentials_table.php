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
        Schema::table('meta_trader_credentials', function (Blueprint $table) {
            $table->foreignUuid('pool_investment_id')->nullable()->after('user_id')->constrained('pool_investments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta_trader_credentials', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\PoolInvestment::class, 'pool_investment_id');
            $table->dropColumn('pool_investment_id');
        });
    }
};
