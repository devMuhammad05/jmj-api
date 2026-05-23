<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('announcement_user');

        Schema::create('personalized_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('personalized_announcement_user', function (Blueprint $table) {
            $table->unsignedBigInteger('personalized_announcement_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['personalized_announcement_id', 'user_id']);
            $table->foreign('personalized_announcement_id', 'pa_user_pa_id_foreign')->references('id')->on('personalized_announcements')->cascadeOnDelete();
            $table->foreign('user_id', 'pa_user_user_id_foreign')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personalized_announcement_user');
        Schema::dropIfExists('personalized_announcements');
    }
};
