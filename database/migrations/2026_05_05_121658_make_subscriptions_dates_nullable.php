<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('starts_at')->nullable()->default(null)->change();
            $table->timestamp('ends_at')->nullable()->default(null)->change();
            $table->boolean('is_active')->default(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('starts_at')->nullable(false)->change();
            $table->timestamp('ends_at')->nullable(false)->change();
            $table->boolean('is_active')->default(true)->change();
        });
    }
};
