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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('action');
            $table->string('type');
            $table->decimal('entry_price', 15, 8);
            $table->decimal('stop_loss', 15, 8);
            $table->decimal('take_profit_1', 15, 8);
            $table->decimal('take_profit_2', 15, 8)->nullable();
            $table->decimal('take_profit_3', 15, 8)->nullable();
            $table->string('status')->default('active');
            $table->decimal('pips_result', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
