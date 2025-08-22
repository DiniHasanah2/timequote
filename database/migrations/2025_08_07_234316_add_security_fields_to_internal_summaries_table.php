<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            // Monitoring
            $table->integer('kl_security_advanced')->nullable();
            $table->integer('cyber_security_advanced')->nullable();
            $table->string('kl_insight_vmonitoring')->default('No');
            $table->string('cyber_insight_vmonitoring')->default('No');

            // Security Service
            $table->integer('kl_cloud_vulnerability')->nullable();
            $table->integer('cyber_cloud_vulnerability')->nullable();

            // Cloud Security
            $table->integer('kl_firewall_fortigate')->nullable();
            $table->integer('cyber_firewall_fortigate')->nullable();
            $table->integer('kl_firewall_opnsense')->nullable();
            $table->integer('cyber_firewall_opnsense')->nullable();
            $table->integer('kl_shared_waf')->nullable();
            $table->integer('cyber_shared_waf')->nullable();
            $table->integer('kl_antivirus')->nullable();
            $table->integer('cyber_antivirus')->nullable();

            // Other Services
            $table->integer('kl_gslb')->nullable();
            $table->integer('cyber_gslb')->nullable();

            // Managed Services
            $table->string('kl_managed_services_1')->default('None');
            $table->string('kl_managed_services_2')->default('None');
            $table->string('kl_managed_services_3')->default('None');
            $table->string('kl_managed_services_4')->default('None');
            $table->string('cyber_managed_services_1')->default('None');
            $table->string('cyber_managed_services_2')->default('None');
            $table->string('cyber_managed_services_3')->default('None');
            $table->string('cyber_managed_services_4')->default('None');
        });
    }

    public function down(): void {
        Schema::table('internal_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'kl_security_advanced',
                'cyber_security_advanced',
                'kl_insight_vmonitoring',
                'cyber_insight_vmonitoring',
                'kl_cloud_vulnerability',
                'cyber_cloud_vulnerability',
                'kl_firewall_fortigate',
                'cyber_firewall_fortigate',
                'kl_firewall_opnsense',
                'cyber_firewall_opnsense',
                'kl_shared_waf',
                'cyber_shared_waf',
                'kl_antivirus',
                'cyber_antivirus',
                'kl_gslb',
                'cyber_gslb',
                'kl_managed_services_1',
                'kl_managed_services_2',
                'kl_managed_services_3',
                'kl_managed_services_4',
                'cyber_managed_services_1',
                'cyber_managed_services_2',
                'cyber_managed_services_3',
                'cyber_managed_services_4',
            ]);
        });
    }
};

