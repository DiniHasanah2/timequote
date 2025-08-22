<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->integer('kl_image_storage')->nullable()->after('kl_snapshot_storage');
            $table->integer('cyber_image_storage')->nullable()->after('cyber_snapshot_storage');
        });
    }

    public function down(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn(['kl_image_storage', 'cyber_image_storage']);
        });
    }
};
