<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MPDRaaSVM extends Model
{
    use HasUuids;

    protected $table = 'mpdraas_vms';
    protected $fillable = [
      'mpdraas_id','version_id','row_no','vm_name','always_on','pin','vcpu','vram',
      'flavour_mapping','system_disk','data_disk','operating_system','rds_count','m_sql',
      'used_system_disk','used_data_disk','solution_type','rto_expected','dd_change',
      'data_change','data_change_size','replication_frequency','num_replication',
      'amount_data_change','replication_bandwidth','rpo_achieved'
    ];

    public function mpdraas(){ return $this->belongsTo(MPDRaaS::class); }
}
