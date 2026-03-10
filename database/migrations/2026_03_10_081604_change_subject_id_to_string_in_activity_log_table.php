<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $table = config("activitylog.table_name", "activity_log");
        $connection = config("activitylog.database_connection");

        Schema::connection($connection)
            ->getConnection()
            ->statement(
                "ALTER TABLE `{$table}` MODIFY `subject_id` VARCHAR(36) NULL",
            );

        Schema::connection($connection)
            ->getConnection()
            ->statement(
                "ALTER TABLE `{$table}` MODIFY `causer_id` VARCHAR(36) NULL",
            );
    }

    public function down(): void
    {
        $table = config("activitylog.table_name", "activity_log");
        $connection = config("activitylog.database_connection");

        Schema::connection($connection)
            ->getConnection()
            ->statement(
                "ALTER TABLE `{$table}` MODIFY `subject_id` BIGINT UNSIGNED NULL",
            );

        Schema::connection($connection)
            ->getConnection()
            ->statement(
                "ALTER TABLE `{$table}` MODIFY `causer_id` BIGINT UNSIGNED NULL",
            );
    }
};
