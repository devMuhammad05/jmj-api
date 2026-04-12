<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->string('wallet_address')->nullable()->after('is_active');
            $table->string('network')->nullable()->after('wallet_address');
            $table->string('bar_code_path')->nullable()->after('network');
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['wallet_address', 'network', 'bar_code_path']);
        });
    }
};
