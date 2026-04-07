<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->morphs('feature'); // feature_id + feature_type
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'feature_id', 'feature_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
