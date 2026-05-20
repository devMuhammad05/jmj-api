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
        Schema::table('pools', function (Blueprint $table) {
            $table->decimal('minimum_investment', 15, 2)->nullable()->change();
            $table->integer('number_of_investors')->nullable()->after('investor_count');
            $table->decimal('each_contribution_amount', 15, 2)->nullable()->after('number_of_investors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pools', function (Blueprint $table) {
            $table->decimal('minimum_investment', 15, 2)->default(1000)->change();
            $table->dropColumn(['number_of_investors', 'each_contribution_amount']);
        });
    }
};
