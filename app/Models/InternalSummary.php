<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InternalSummary extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'version_id',
        'project_id',
        'customer_id',
        'presale_id',

        // KL
        'kl_bandwidth',
        'kl_bandwidth_with_antiddos',
        'kl_included_elastic_ip',
        'kl_elastic_ip',
        'kl_elastic_load_balancer',
        'kl_direct_connect_virtual',
        'kl_l2br_instance',
        'kl_virtual_private_leased_line',
        'kl_vpll_l2br',
        'kl_nat_gateway_small',
        'kl_nat_gateway_medium',
        'kl_nat_gateway_large',
        'kl_nat_gateway_xlarge',

        // Cyber
        'cyber_bandwidth',
        'cyber_bandwidth_with_antiddos',
        'cyber_included_elastic_ip',
        'cyber_elastic_ip',
        'cyber_elastic_load_balancer',
        'cyber_direct_connect_virtual',
        'cyber_l2br_instance',
        'cyber_nat_gateway_small',
        'cyber_nat_gateway_medium',
        'cyber_nat_gateway_large',
        'cyber_nat_gateway_xlarge',


    // Storage
    'kl_scalable_file_service',
    'cyber_scalable_file_service',
    'kl_object_storage_service',
    'cyber_object_storage_service',

    // EVS & Snapshot
    'kl_evs',
    'cyber_evs',
    'kl_snapshot_storage',
    'cyber_snapshot_storage',
     'kl_image_storage',
    'cyber_image_storage',

    // Professional services
    'mandays',
    'kl_license_count',
    'cyber_license_count',
    'kl_duration',
    'cyber_duration',
    
    // Monitoring
'kl_security_advanced',
'cyber_security_advanced',
'kl_insight_vmonitoring',
'cyber_insight_vmonitoring',

// Security Service
'kl_cloud_vulnerability',
'cyber_cloud_vulnerability',

// Cloud Security
'kl_firewall_fortigate',
'cyber_firewall_fortigate',
'kl_firewall_opnsense',
'cyber_firewall_opnsense',
'kl_shared_waf',
'cyber_shared_waf',
'kl_antivirus',
'cyber_antivirus',

// Other Services
'kl_gslb',
'cyber_gslb',

// Managed Services
'kl_managed_services_1',
'kl_managed_services_2',
'kl_managed_services_3',
'kl_managed_services_4',
'cyber_managed_services_1',
'cyber_managed_services_2',
'cyber_managed_services_3',
'cyber_managed_services_4',

  'kl_windows_std',
    'cyber_windows_std',
    'kl_windows_dc',
    'cyber_windows_dc',
    'kl_rds',
    'cyber_rds',
    'kl_sql_web',
    'cyber_sql_web',
    'kl_sql_std',
    'cyber_sql_std',
    'kl_sql_ent',
    'cyber_sql_ent',
    'kl_rhel_1_8',
    'cyber_rhel_1_8',
    'kl_rhel_9_127',
    'cyber_rhel_9_127',
      'ecs_flavour_mapping',
        'ecs_vcpu',
        'ecs_vram',

         'kl_full_backup_capacity',
    'cyber_full_backup_capacity',
    'kl_incremental_backup_capacity',
    'cyber_incremental_backup_capacity',
    'kl_replication_retention_capacity',
    'cyber_replication_retention_capacity',

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
        'cyber_dr_managed_service',


    // DR Network & Security 
    'kl_dr_vpll','cyber_dr_vpll',
    'kl_dr_elastic_ip','cyber_dr_elastic_ip',
    'kl_dr_bandwidth','cyber_dr_bandwidth',
    'kl_dr_bandwidth_antiddos','cyber_dr_bandwidth_antiddos',
    'kl_dr_firewall_fortigate','cyber_dr_firewall_fortigate',
    'kl_dr_firewall_opnsense','cyber_dr_firewall_opnsense',




    // DR Licenses
    'kl_dr_license_months','cyber_dr_license_months',
    'kl_dr_windows_std','cyber_dr_windows_std',
    'kl_dr_windows_dc','cyber_dr_windows_dc',
    'kl_dr_rds','cyber_dr_rds',
    'kl_dr_sql_web','cyber_dr_sql_web',
    'kl_dr_sql_std','cyber_dr_sql_std',
    'kl_dr_sql_ent','cyber_dr_sql_ent',
    'kl_dr_rhel_1_8','cyber_dr_rhel_1_8',
    'kl_dr_rhel_9_127','cyber_dr_rhel_9_127',







    ];


     protected $casts = [
        'vcpu_count' => 'integer',
        'vram_count' => 'integer',
        'worker_flavour_mapping' => 'array',//'array' if storing JSON in the database
    ];
     



    public function version()
{
    return $this->belongsTo(Version::class);
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
