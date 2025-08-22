<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ecs_configurations', function (Blueprint $table) {
            $table->integer('estimated_storage_full_backup')->nullable()->after('full_backup_total_retention_full_copies');
            $table->integer('estimated_storage_incremental_backup')->nullable()->after('incremental_backup_total_retention_incremental_copies');
            $table->integer('estimated_storage_csbs_replication')->nullable()->after('rpo');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecs_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_storage_full_backup',
                'estimated_storage_incremental_backup',
                'estimated_storage_csbs_replication'
            ]);
        });
    }
};
