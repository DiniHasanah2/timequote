<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Version;
use App\Models\User;


class MPDRaaS extends Model
{
    protected $table = 'mpdraas';

    protected $fillable = [
        // Relationships
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',

        'mpdraas_activation_days',
        'starter_promotion',
        'mpdraas_location',

        'num_proxy',
        'vm_name',
        'always_on',
        'pin',
        'vcpu',
        'vram',
        'flavour_mapping',
        'system_disk',
        'data_disk',
        'operating_system',
        'rds_count',
        'm_sql',
        'used_system_disk',
        'used_data_disk',
        'solution_type',
        'rto_expected',
        'dd_change',
        'data_change',
        'data_change_size',
        'replication_frequency',
        'num_replication',
        'amount_data_change',
        'replication_bandwidth',

        'rpo_achieved',
        'ddos_requirement',
        'bandwidth_requirement',

        'main',
        'used',
        'delta',
        'total_replication',
         'dr_network',








    ];

     protected $casts = [
        /*'vcpu_count' => 'integer',
        'vram_count' => 'integer',
        'worker_flavour_mapping' => 'array',//'array' if storing JSON in the database*/
         'dr_network' => 'array',
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