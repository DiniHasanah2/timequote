<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            // Storage
            $table->integer('kl_scalable_file_service')->nullable();
            $table->integer('cyber_scalable_file_service')->nullable();
            $table->integer('kl_object_storage_service')->nullable();
            $table->integer('cyber_object_storage_service')->nullable();

            // EVS + Snapshot
            $table->integer('kl_evs')->nullable();
            $table->integer('cyber_evs')->nullable();
            $table->integer('kl_snapshot_storage')->nullable();
            $table->integer('cyber_snapshot_storage')->nullable();

            // Professional Services
            $table->integer('mandays')->nullable();
            $table->integer('kl_license_count')->nullable();
            $table->integer('cyber_license_count')->nullable();
            $table->integer('kl_duration')->nullable();
            $table->integer('cyber_duration')->nullable();
        });
    }

    public function down(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'kl_scalable_file_service',
                'cyber_scalable_file_service',
                'kl_object_storage_service',
                'cyber_object_storage_service',
                'kl_evs',
                'cyber_evs',
                'kl_snapshot_storage',
                'cyber_snapshot_storage',
                'mandays',
                'kl_license_count',
                'cyber_license_count',
                'kl_duration',
                'cyber_duration',
            ]);
        });
    }
};
