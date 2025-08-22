<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecs_configurations', function (Blueprint $table) {
            $table->id('id');
            
            // Foreign keys
            $table->uuid('project_id');
            $table->uuid('version_id');
            $table->uuid('customer_id');
            $table->uuid('presale_id');
          

            // Basic Info
            $table->string('region')->default('Kuala Lumpur');
            $table->string('vm_name')->nullable();

            // ECS Fields
            $table->string('ecs_pin')->default('No');
            $table->string('ecs_gpu')->default('No');
            $table->string('ecs_ddh')->default('No');
            $table->integer('ecs_vcpu')->nullable();
            $table->integer('ecs_vram')->nullable();
            $table->text('ecs_flavour_mapping')->nullable(); // JSON atau string

            // Container Worker
            $table->integer('vcpu_count')->nullable();
            $table->integer('vram_count')->nullable();
            $table->text('worker_flavour_mapping')->nullable();

            // Storage
            $table->integer('storage_system_disk')->nullable();
            $table->integer('storage_data_disk')->nullable();

            // License
            $table->string('license_operating_system')->default('Linux');
            $table->string('license_rds_license')->nullable();
            $table->string('license_microsoft_sql')->default('None');

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

            // Full Backup
            $table->integer('full_backup_daily')->nullable();
            $table->integer('full_backup_weekly')->nullable();
            $table->integer('full_backup_monthly')->nullable();
            $table->integer('full_backup_yearly')->nullable();
            $table->integer('full_backup_total_retention_full_copies')->nullable();

            // Incremental Backup
            $table->integer('incremental_backup_daily')->nullable();
            $table->integer('incremental_backup_weekly')->nullable();
            $table->integer('incremental_backup_monthly')->nullable();
            $table->integer('incremental_backup_yearly')->nullable();
            $table->integer('incremental_backup_total_retention_incremental_copies')->nullable();

            // CSBS Replication
            $table->string('required')->default('Yes');
            $table->integer('total_replication_copy_retained_second_site')->nullable();
            $table->integer('additional_storage')->nullable();
            $table->integer('rto')->nullable();
            $table->string('rpo')->default('N/A');
            

            // DR Requirement
            $table->string('dr_activation')->default('Yes');
            $table->string('seed_vm_required')->default('Yes');

            // Replication using CSDR
            $table->string('csdr_needed')->default('Yes');
            $table->integer('csdr_storage')->nullable();

            $table->timestamps();
  
        });

  Schema::table('ecs_configurations', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('presale_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreignUuid('presale_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecs_configurations');
    }
};