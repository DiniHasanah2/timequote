<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('internal_summaries', function (Blueprint $table) {
        // Cold DR Days
        $table->integer('kl_cold_dr_days')->nullable()->default(0); // Untuk Kuala Lumpur
        $table->integer('cyber_cold_dr_days')->nullable()->default(0); // Untuk Cyberjaya

        // Cold DR – Seeding VM
        $table->integer('kl_cold_dr_seeding_vm')->nullable()->default(0); // Untuk Kuala Lumpur
        $table->integer('cyber_cold_dr_seeding_vm')->nullable()->default(0); // Untuk Cyberjaya

        // Cloud Server Disaster Recovery Storage
        $table->decimal('kl_dr_storage', 10, 2)->nullable()->default(0); // Untuk Kuala Lumpur
        $table->decimal('cyber_dr_storage', 10, 2)->nullable()->default(0); // Untuk Cyberjaya

        // Cloud Server Disaster Recovery Replication
        $table->integer('kl_dr_replication')->nullable()->default(0); // Untuk Kuala Lumpur
        $table->integer('cyber_dr_replication')->nullable()->default(0); // Untuk Cyberjaya

        // Cloud Server Disaster Recovery Days (DR Declaration)
        $table->integer('kl_dr_declaration')->nullable()->default(0); // Untuk Kuala Lumpur
        $table->integer('cyber_dr_declaration')->nullable()->default(0); // Untuk Cyberjaya

        // Cloud Server Disaster Recovery Managed Service – Per Day
        $table->integer('kl_dr_managed_service')->nullable()->default(0); // Untuk Kuala Lumpur
        $table->integer('cyber_dr_managed_service')->nullable()->default(0); // Untuk Cyberjaya
    });
}

public function down()
{
    Schema::table('internal_summaries', function (Blueprint $table) {
        // Remove the fields if we roll back
        $table->dropColumn([
            'kl_cold_dr_days',
            'cyber_cold_dr_days',
            'kl_cold_dr_seeding_vm',
            'cyber_cold_dr_seeding_vm',
            'kl_dr_storage',
            'cyber_dr_storage',
            'kl_dr_replication',
            'cyber_dr_replication',
            'kl_dr_declaration',
            'cyber_dr_declaration',
            'kl_dr_managed_service',
            'cyber_dr_managed_service'
        ]);
    });
}

};
