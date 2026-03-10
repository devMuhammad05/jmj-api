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
            if (Schema::hasColumn('pools', 'last_return')) {
                $table->dropColumn('last_return');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pools', function (Blueprint $table) {
            if (! Schema::hasColumn('pools', 'last_return')) {
                $table->decimal('last_return', 8, 2)->nullable();
            }
        });
    }
};
