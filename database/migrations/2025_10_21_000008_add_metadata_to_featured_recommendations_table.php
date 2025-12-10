<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('featured_recommendations', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('note');
            $table->integer('priority')->default(0)->after('active');
            $table->timestamp('starts_at')->nullable()->after('priority');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('featured_recommendations', function (Blueprint $table) {
            $table->dropColumn(['active', 'priority', 'starts_at', 'ends_at']);
        });
    }
};