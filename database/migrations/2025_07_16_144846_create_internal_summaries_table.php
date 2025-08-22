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
        Schema::create('internal_summaries', function (Blueprint $table) {

         $table->id();
            $table->uuid('project_id');
             $table->uuid('version_id')->unique();
            $table->uuid('customer_id');
            $table->uuid('presale_id');


            // Professional Services
            $table->integer('managed_operating_system')->nullable()->default(0);
            $table->integer('managed_backup_and_restore')->nullable()->default(0);
            $table->integer('managed_patching')->nullable()->default(0);
            $table->integer('managed_dr')->nullable()->default(0);
            $table->string('deployment_method')->default('self-provisioning');
            $table->integer('mandays')->nullable();
            $table->integer('content_delivery_network')->nullable();
            
            // DR Settings
            $table->integer('dr_activation_days')->nullable();
            $table->string('dr_location')->default('Kuala Lumpur');
            $table->integer('db_bandwidth')->nullable();
            $table->string('dr_bandwidth_type')->default('bandwidth');
            $table->integer('elastic_ip_dr')->nullable();
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





            //Monitoring
            $table->integer('kl_security_advanced')->nullable();
            $table->integer('cyber_security_advanced')->nullable();
            $table->string('kl_insight_vmonitoring')->default('No');
            $table->string('cyber_insight_vmonitoring')->default('No');
          
            
            // Security Service
            $table->integer('kl_cloud_vulnerability')->nullable();
            $table->integer('cyber_cloud_vulnerability')->nullable();


            // Cloud Security
            $table->integer( 'kl_firewall_fortigate')->nullable();
            $table->integer( 'cyber_firewall_fortigate')->nullable();
            $table->integer('kl_firewall_opnsense')->nullable();
            $table->integer('cyber_firewall_opnsense')->nullable();
            $table->integer('kl_shared_waf')->nullable();
            $table->integer('cyber_shared_waf')->nullable();
            $table->integer('kl_antivirus')->nullable();
            $table->integer('cyber_antivirus')->nullable();
             
            // Other Services
            $table->integer('kl_gslb')->nullable();
            $table->integer('cyber_gslb')->nullable();




            $table->text('ecs_flavour_mapping')->nullable(); // JSON atau string

            // Container Worker
            $table->integer('vcpu_count')->nullable();
            $table->integer('vram_count')->nullable();
            $table->text('worker_flavour_mapping')->nullable();

            // Storage
            $table->integer('storage_system_disk')->nullable();
            $table->integer('storage_data_disk')->nullable();

            // License
            $table->string('license_operating_system')->default('Microsoft Windows Std');
            $table->string('license_rds_license')->nullable();
            $table->string('license_microsoft_sql')->default('Standard');

            // Image/Snapshot
            $table->integer('snapshot_copies')->nullable();
            $table->integer('additional_capacity')->nullable();
            $table->integer('image_copies')->nullable();

            // CSBS
            $table->string('csbs_standard_policy')->default('No Backup');
            $table->integer('csbs_local_retention_copies')->nullable();
            $table->integer('csbs_total_storage')->nullable();
            $table->integer('csbs_initial_data_size')->nullable();
            $table->integer('csbs_incremental_change')->nullable();
            $table->integer('csbs_estimated_incremental_data_change')->nullable();

          
            $table->integer('full_backup_total_retention_full_copies')->nullable();

           
            $table->integer('incremental_backup_total_retention_incremental_copies')->nullable();

            // CSBS Replication
            $table->string('required')->default('Yes');
            $table->integer('total_replication_copy_retained_second_site')->nullable();
            $table->integer('additional_storage')->nullable();
          

            // DR Requirement
            $table->string('dr_activation')->default('Yes');
            $table->string('seed_vm_required')->default('Yes');

            // Replication using CSDR
            $table->string('csdr_needed')->default('Yes');
            $table->integer('csdr_storage')->nullable();






            
            $table->timestamps(); //created at + updated at

 


           
        });


         Schema::table('internal_summaries', function (Blueprint $table) {
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
        Schema::dropIfExists('internal_summaries');
    }
};
