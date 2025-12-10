<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_course_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
            $table->string('type'); // view|enroll|complete|rate
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5 when type=rate
            $table->timestamps();

            $table->index(['user_id', 'course_id', 'type']);
            $table->index(['course_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_course_activities');
    }
};