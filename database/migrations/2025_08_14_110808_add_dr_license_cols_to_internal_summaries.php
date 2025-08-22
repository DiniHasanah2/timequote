<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            // DR License Month
            $table->integer('kl_dr_license_months')->default(0);
            $table->integer('cyber_dr_license_months')->default(0);

            // DR License counts (Unit per month)
            $table->integer('kl_dr_windows_std')->default(0);
            $table->integer('cyber_dr_windows_std')->default(0);
            $table->integer('kl_dr_windows_dc')->default(0);
            $table->integer('cyber_dr_windows_dc')->default(0);
            $table->integer('kl_dr_rds')->default(0);
            $table->integer('cyber_dr_rds')->default(0);
            $table->integer('kl_dr_sql_web')->default(0);
            $table->integer('cyber_dr_sql_web')->default(0);
            $table->integer('kl_dr_sql_std')->default(0);
            $table->integer('cyber_dr_sql_std')->default(0);
            $table->integer('kl_dr_sql_ent')->default(0);
            $table->integer('cyber_dr_sql_ent')->default(0);
            $table->integer('kl_dr_rhel_1_8')->default(0);
            $table->integer('cyber_dr_rhel_1_8')->default(0);
            $table->integer('kl_dr_rhel_9_127')->default(0);
            $table->integer('cyber_dr_rhel_9_127')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'kl_dr_license_months','cyber_dr_license_months',
                'kl_dr_windows_std','cyber_dr_windows_std',
                'kl_dr_windows_dc','cyber_dr_windows_dc',
                'kl_dr_rds','cyber_dr_rds',
                'kl_dr_sql_web','cyber_dr_sql_web',
                'kl_dr_sql_std','cyber_dr_sql_std',
                'kl_dr_sql_ent','cyber_dr_sql_ent',
                'kl_dr_rhel_1_8','cyber_dr_rhel_1_8',
                'kl_dr_rhel_9_127','cyber_dr_rhel_9_127',
            ]);
        });
    }
};
