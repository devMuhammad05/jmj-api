<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Drop the global unique on level — level is only unique per type
            $table->dropUnique(['level']);

            $table->string('type')->default('signals')->after('slug')->index();

            // level 1 = free, level 2 = pro — unique per type
            $table->unique(['type', 'level']);
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropUnique(['type', 'level']);
            $table->dropColumn('type');
            $table->unique('level');
        });
    }
};
