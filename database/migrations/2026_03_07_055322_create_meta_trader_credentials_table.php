<?php

use App\Enums\MetaTraderPlatformType;
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
        Schema::create('meta_trader_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->string('mt_account_number', 50);
            $table->string('mt_password');
            $table->string('mt_server', 100);
            $table->string('platform_type')->default(MetaTraderPlatformType::MT5->value);
            $table->decimal('initial_deposit', 10, 2);
            $table->string('risk_level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_trader_credentials');
    }
};
