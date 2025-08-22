<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            // DR Network & Security (semua default 0 untuk selamat)
            $table->integer('kl_dr_vpll')->default(0);
            $table->integer('cyber_dr_vpll')->default(0);

            $table->integer('kl_dr_elastic_ip')->default(0);
            $table->integer('cyber_dr_elastic_ip')->default(0);

            $table->integer('kl_dr_bandwidth')->default(0);
            $table->integer('cyber_dr_bandwidth')->default(0);

            $table->integer('kl_dr_bandwidth_antiddos')->default(0);
            $table->integer('cyber_dr_bandwidth_antiddos')->default(0);

            $table->integer('kl_dr_firewall_fortigate')->default(0);
            $table->integer('cyber_dr_firewall_fortigate')->default(0);
            $table->integer('kl_dr_firewall_opnsense')->default(0);
            $table->integer('cyber_dr_firewall_opnsense')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'kl_dr_vpll','cyber_dr_vpll',
                'kl_dr_elastic_ip','cyber_dr_elastic_ip',
                'kl_dr_bandwidth','cyber_dr_bandwidth',
                'kl_dr_bandwidth_antiddos','cyber_dr_bandwidth_antiddos',
                'kl_dr_firewall_fortigate','cyber_dr_firewall_fortigate',
                'kl_dr_firewall_opnsense','cyber_dr_firewall_opnsense',
            ]);
        });
    }
};
