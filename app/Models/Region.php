<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Version;
use App\Models\User;

class Region extends Model
{
    protected $fillable = [
        // Relationships
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',
        
        // Professional Services
        'region',
        'deployment_method',
        'mandays',
        'scope_of_work',
        'kl_content_delivery_network',
        'cyber_content_delivery_network',
         'kl_license_count',
        'cyber_license_count',
        'kl_duration',
        'cyber_duration',
        
        // DR Settings
        'kl_dr_activation_days',
        'cyber_dr_activation_days',
        'dr_location',
        'kl_db_bandwidth',
         'cyber_db_bandwidth',
        'dr_bandwidth_type',
        'kl_elastic_ip_dr',
          'cyber_elastic_ip_dr',
        'tier1_dr_security',
        'tier2_dr_security',
        
        // Network - KL
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
        
        // Network - Cyber
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

     public function serviceDescription()
    {
        return view('projects.region.service_description');
    }
}