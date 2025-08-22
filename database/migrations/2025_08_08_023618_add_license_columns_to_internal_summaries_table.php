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
        Schema::table('internal_summaries', function (Blueprint $table) {
    $table->integer('kl_windows_std')->nullable();
    $table->integer('cyber_windows_std')->nullable();
    $table->integer('kl_windows_dc')->nullable();
    $table->integer('cyber_windows_dc')->nullable();
    $table->integer('kl_rds')->nullable();
    $table->integer('cyber_rds')->nullable();
    $table->integer('kl_sql_web')->nullable();
    $table->integer('cyber_sql_web')->nullable();
    $table->integer('kl_sql_std')->nullable();
    $table->integer('cyber_sql_std')->nullable();
    $table->integer('kl_sql_ent')->nullable();
    $table->integer('cyber_sql_ent')->nullable();
    $table->integer('kl_rhel_1_8')->nullable();
    $table->integer('cyber_rhel_1_8')->nullable();
    $table->integer('kl_rhel_9_127')->nullable();
    $table->integer('cyber_rhel_9_127')->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('internal_summaries', function (Blueprint $table) {
    $table->dropColumn([
        'kl_windows_std', 'cyber_windows_std',
        'kl_windows_dc', 'cyber_windows_dc',
        'kl_rds', 'cyber_rds',
        'kl_sql_web', 'cyber_sql_web',
        'kl_sql_std', 'cyber_sql_std',
        'kl_sql_ent', 'cyber_sql_ent',
        'kl_rhel_1_8', 'cyber_rhel_1_8',
        'kl_rhel_9_127', 'cyber_rhel_9_127',
    ]);
});

    }
};
