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
        Schema::create('account_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mt_account_id')->constrained('meta_trader_credentials')->cascadeOnDelete();
            $table->decimal('balance', 15, 2);
            $table->decimal('equity', 15, 2);
            $table->decimal('margin', 15, 2);
            $table->decimal('free_margin', 15, 2);
            $table->decimal('margin_level', 10, 4)->nullable();
            $table->unsignedInteger('leverage');
            $table->string('currency', 10);
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_snapshots');
    }
};
