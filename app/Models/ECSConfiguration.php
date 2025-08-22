<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Version;
use App\Models\User;

class ECSConfiguration extends Model
{
  protected $table = 'ecs_configurations';
    protected $fillable = [
       // Relationships
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',

         'region',
            'vm_name',
          
            // ECS
            'ecs_pin',
            'ecs_gpu' ,
            'ecs_ddh' ,
            'ecs_vcpu' ,
            'ecs_vram' ,
            'ecs_flavour_mapping' ,

             //Container Worker
            'vcpu_count',
             'vram_count',
             'worker_flavour_mapping',

            // Storage
            'storage_system_disk',
            'storage_data_disk',

            // License
            'license_operating_system',
            'license_rds_license',
            'license_microsoft_sql' ,

             // Image and Snapshot
            'snapshot_copies' ,
            'additional_capacity' ,
            'image_copies' ,

            //Cloud Server Backup Service (CSBS)
            'csbs_standard_policy',
            'csbs_local_retention_copies' ,
            'csbs_total_storage' ,
            'csbs_initial_data_size' ,
            'csbs_incremental_change' ,
            'csbs_estimated_incremental_data_change',

            // Full Backup
            'full_backup_daily' ,
            'full_backup_weekly' ,
            'full_backup_monthly' ,
            'full_backup_yearly' ,
            'full_backup_total_retention_full_copies' ,

              // Incremental Backup
            'incremental_backup_daily',
            'incremental_backup_weekly',
            'incremental_backup_monthly' ,
            'incremental_backup_yearly' ,
            'incremental_backup_total_retention_incremental_copies',

              // CSBS Replication
            'required' ,
            'total_replication_copy_retained_second_site' ,
            'additional_storage',
            'rto' ,
            'rpo' ,

            'estimated_storage_full_backup',
            'estimated_storage_incremental_backup',
            'estimated_storage_csbs_replication',



              // DR Requirement
              'ecs_dr',
            'dr_activation', //DR Requirement
            'seed_vm_required' , //Cold DR

             // Replication using CSDR
            'csdr_needed', //warm DR
            'csdr_storage' , //warm DR

            'suggestion_estimated_storage_full_backup',
             'suggestion_estimated_storage_incremental_backup',
              'suggestion_estimated_storage_csbs_replication',
    ];

     protected $casts = [
        'vcpu_count' => 'integer',
        'vram_count' => 'integer',
        'worker_flavour_mapping' => 'array',//'array' if storing JSON in the database
    ];
     
   public function version()
{
    return $this->belongsTo(Version::class, 'version_id');
}


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function presale()
    {
        return $this->belongsTo(User::class, 'presale_id');
    }

    
}