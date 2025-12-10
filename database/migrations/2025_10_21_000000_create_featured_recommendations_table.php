<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('featured_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unique('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_recommendations');
    }
};