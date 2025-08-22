<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('internal_summaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('version_id');
            $table->uuid('project_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->uuid('presale_id')->nullable();

            // Network - KL
            $table->integer('kl_bandwidth')->nullable();
            $table->integer('kl_bandwidth_with_antiddos')->nullable();
            $table->integer('kl_included_elastic_ip')->nullable();
            $table->integer('kl_elastic_ip')->nullable();
            $table->integer('kl_elastic_load_balancer')->nullable();
            $table->integer('kl_direct_connect_virtual')->nullable();
            $table->integer('kl_l2br_instance')->nullable();
            $table->integer('kl_virtual_private_leased_line')->nullable();
            $table->integer('kl_vpll_l2br')->nullable();
            $table->integer('kl_nat_gateway_small')->nullable();
            $table->integer('kl_nat_gateway_medium')->nullable();
            $table->integer('kl_nat_gateway_large')->nullable();
            $table->integer('kl_nat_gateway_xlarge')->nullable();

            // Network - Cyber
            $table->integer('cyber_bandwidth')->nullable();
            $table->integer('cyber_bandwidth_with_antiddos')->nullable();
            $table->integer('cyber_included_elastic_ip')->nullable();
            $table->integer('cyber_elastic_ip')->nullable();
            $table->integer('cyber_elastic_load_balancer')->nullable();
            $table->integer('cyber_direct_connect_virtual')->nullable();
            $table->integer('cyber_l2br_instance')->nullable();
            $table->integer('cyber_nat_gateway_small')->nullable();
            $table->integer('cyber_nat_gateway_medium')->nullable();
            $table->integer('cyber_nat_gateway_large')->nullable();
            $table->integer('cyber_nat_gateway_xlarge')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('internal_summaries');
    }
};

