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
        Schema::table('pool_investments', function (Blueprint $table) {
            // Add amount_paid if not already present (fresh installs get it from the create migration)
            if (! Schema::hasColumn('pool_investments', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('contribution');
            }

            // Drop legacy investment_proof_path only if it exists (moved to payment_proofs table)
            if (Schema::hasColumn('pool_investments', 'investment_proof_path')) {
                $table->dropColumn('investment_proof_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pool_investments', function (Blueprint $table) {
            if (Schema::hasColumn('pool_investments', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
        });
    }
};
