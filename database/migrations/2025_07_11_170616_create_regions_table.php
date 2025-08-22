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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->uuid('project_id');
             $table->uuid('version_id')->unique();
            $table->uuid('customer_id');
            $table->uuid('presale_id');
            // Professional Services
            $table->string('region')->default('Kuala Lumpur');
            $table->string('deployment_method')->default('self-provisioning');
            $table->integer('mandays')->nullable();
              $table->integer('kl_license_count')->nullable();
            $table->integer('cyber_license_count')->nullable();
            $table->integer('kl_duration')->nullable();
            $table->integer('cyber_duration')->nullable();


            //$table->string('region');
            $table->integer('kl_content_delivery_network')->nullable();
            $table->integer('cyber_content_delivery_network')->nullable();
            
            // DR Settings
            $table->integer('kl_dr_activation_days')->nullable();
            $table->integer('cyber_dr_activation_days')->nullable();
            $table->string('dr_location')->default('Kuala Lumpur');
            $table->integer('kl_db_bandwidth')->nullable();
            $table->integer('cyber_db_bandwidth')->nullable();
            $table->string('dr_bandwidth_type')->default('bandwidth');
            $table->integer('kl_elastic_ip_dr')->nullable();
            $table->integer('cyber_elastic_ip_dr')->nullable();
            $table->string('tier1_dr_security')->default('none');
            $table->string('tier2_dr_security')->default('none');
            
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
            
            // Storage
            $table->integer('kl_scalable_file_service')->nullable();
            $table->integer('cyber_scalable_file_service')->nullable();
            $table->integer('kl_object_storage_service')->nullable();
            $table->integer('cyber_object_storage_service')->nullable();
            
            $table->timestamps(); //created at + updated at

 


           
        });


         Schema::table('regions', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('presale_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};