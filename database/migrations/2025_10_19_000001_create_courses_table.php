<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->string('tags')->nullable(); // comma-separated tags
            $table->text('description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('enrollments_count')->default(0);
            $table->float('rating')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};