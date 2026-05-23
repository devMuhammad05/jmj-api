<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('JMJ Investment');
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('deriv_referral_url')->nullable();
            $table->string('youtube_tutorials_url')->nullable();
            $table->text('results_timeline')->nullable();
            $table->decimal('minimum_deposit', 15, 2)->nullable();
            $table->decimal('maximum_deposit', 15, 2)->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
