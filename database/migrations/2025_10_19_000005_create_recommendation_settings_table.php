<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recommendation_settings', function (Blueprint $table) {
            $table->id();
            $table->float('views_weight')->default(1.0);
            $table->float('enrollments_weight')->default(2.0);
            $table->float('activity_weight')->default(1.0);
            $table->unsignedInteger('default_trending_days')->default(7);
            $table->unsignedInteger('personalized_top_tags_limit')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_settings');
    }
};