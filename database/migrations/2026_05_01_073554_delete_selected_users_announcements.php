<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('announcements')
            ->where('target_audience', 'selected_users')
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
