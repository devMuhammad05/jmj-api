<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing signal status values from hit_tp to tp and hit_sl to sl
        DB::table('signals')
            ->where('status', 'hit_tp')
            ->update(['status' => 'tp']);

        DB::table('signals')
            ->where('status', 'hit_sl')
            ->update(['status' => 'sl']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old values
        DB::table('signals')
            ->where('status', 'tp')
            ->update(['status' => 'hit_tp']);

        DB::table('signals')
            ->where('status', 'sl')
            ->update(['status' => 'hit_sl']);
    }
};
