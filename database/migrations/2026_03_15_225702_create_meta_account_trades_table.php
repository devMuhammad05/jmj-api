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
        Schema::create('meta_account_trades', function (Blueprint $table) {
            $table->id();
            $table->uuid('account_id');  // MetaAPI UUID
            $table->string('trade_id');  // MetaAPI trade unique ID
            $table->string('type');      // e.g., DEAL_TYPE_BALANCE
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('volume', 15, 2)->nullable(); // if relevant
            $table->decimal('price_open', 15, 5)->nullable();
            $table->decimal('price_close', 15, 5)->nullable();
            $table->timestamp('open_time')->nullable();
            $table->timestamp('close_time')->nullable();
            $table->json('extra')->nullable(); // for any extra trade details
            $table->timestamps();

            $table->index('account_id');
            $table->unique(['account_id', 'trade_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_account_trades');
    }
};
