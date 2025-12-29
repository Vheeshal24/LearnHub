<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Check if the table exists before trying to modify it
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                // Use jsonb for better performance in PostgreSQL
                $table->jsonb('quiz_json')->nullable()->after('content_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('quiz_json');
            });
        }
    }
};