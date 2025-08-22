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
        Schema::create('security_services', function (Blueprint $table) {
            $table->id();
            $table->uuid('project_id');
             $table->uuid('version_id')->unique();
            $table->uuid('customer_id');
            $table->uuid('presale_id');


            //Managed Services
            $table->string('kl_managed_services_1')->default('None');
            $table->string('kl_managed_services_2')->default('None');
            $table->string('kl_managed_services_3')->default('None');
            $table->string('kl_managed_services_4')->default('None');
            $table->string('cyber_managed_services_1')->default('None');
            $table->string('cyber_managed_services_2')->default('None');
            $table->string('cyber_managed_services_3')->default('None');
            $table->string('cyber_managed_services_4')->default('None');

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





   
 
            
         
            
            $table->timestamps(); //created at + updated at

 


           
        });


         Schema::table('security_services', function (Blueprint $table) {
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
        Schema::dropIfExists('security_services');
    }
};



