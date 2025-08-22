<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->unsignedBigInteger('kl_full_backup_capacity')->default(0)->after('cyber_image_storage');
            $table->unsignedBigInteger('cyber_full_backup_capacity')->default(0)->after('kl_full_backup_capacity');
            $table->unsignedBigInteger('kl_incremental_backup_capacity')->default(0)->after('cyber_full_backup_capacity');
            $table->unsignedBigInteger('cyber_incremental_backup_capacity')->default(0)->after('kl_incremental_backup_capacity');
            $table->unsignedBigInteger('kl_replication_retention_capacity')->default(0)->after('cyber_incremental_backup_capacity');
            $table->unsignedBigInteger('cyber_replication_retention_capacity')->default(0)->after('kl_replication_retention_capacity');
        });
    }

    public function down(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'kl_full_backup_capacity',
                'cyber_full_backup_capacity',
                'kl_incremental_backup_capacity',
                'cyber_incremental_backup_capacity',
                'kl_replication_retention_capacity',
                'cyber_replication_retention_capacity',
            ]);
        });
    }
};
