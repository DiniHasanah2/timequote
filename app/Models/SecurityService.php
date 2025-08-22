<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Version;
use App\Models\User;


class SecurityService extends Model
{
    protected $fillable = [
        // Relationships
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',

        //Managed Services
        'kl_managed_services_1',
        'kl_managed_services_2',
        'kl_managed_services_3',
        'kl_managed_services_4',
        'cyber_managed_services_1',
        'cyber_managed_services_2',
        'cyber_managed_services_3',
        'cyber_managed_services_4',
        
        // Monitoring
        'kl_security_advanced',
        'cyber_security_advanced',
        'kl_insight_vmonitoring',
        'cyber_insight_vmonitoring',

        //Security Service
        'kl_cloud_vulnerability',
        'cyber_cloud_vulnerability',

        //Cloud Security
        'kl_firewall_fortigate',
        'cyber_firewall_fortigate',
        'kl_firewall_opnsense',
        'cyber_firewall_opnsense',
        'kl_shared_waf',
        'cyber_shared_waf',
        'kl_antivirus',
        'cyber_antivirus',

        //Other Services
        'kl_gslb',
        'cyber_gslb',





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