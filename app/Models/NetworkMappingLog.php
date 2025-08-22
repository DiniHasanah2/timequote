<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkMappingLog extends Model
{
    protected $fillable = [
        'network_mapping_id','action','old_values','new_values',
        'user_id','ip_address','user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        // optional
        // 'user_id' => 'string',
    ];


    public function networkMapping()
    {
        return $this->belongsTo(NetworkMapping::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
