<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->boolean('is_logged')->default(false)->after('version_id');
            $table->timestamp('logged_at')->nullable()->after('is_logged');
        });
    }

    public function down(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn(['is_logged','logged_at']);
        });
    }
};
