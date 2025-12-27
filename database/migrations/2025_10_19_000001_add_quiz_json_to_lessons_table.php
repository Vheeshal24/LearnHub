<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
         // Temporarily disabled because the lessons table does not exist yet.
    // Schema::table('lessons', function (Blueprint $table) {
    //     $table->longText('quiz_json')->nullable()->after('content_url');
    // });
    }

    public function down(): void
    {
        // Schema::table('lessons', function (Blueprint $table) {
    //     $table->dropColumn('quiz_json');
    // });
    }
};