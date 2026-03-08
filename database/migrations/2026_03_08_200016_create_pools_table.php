<?php

use App\Enums\PoolStatus;
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
        Schema::create('pools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('investor_count')->default(0);
            $table->decimal('last_return', 8, 2)->nullable();
            $table->decimal('minimum_investment', 15, 2)->default(1000);
            $table->string('status')->default(PoolStatus::ACTIVE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pools');
    }
};
