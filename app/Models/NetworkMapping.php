<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NetworkMapping extends Model
{
    use HasFactory;

    protected $fillable = ['network_code','min_bw','max_bw','eip_foc','anti_ddos'];

    protected $casts = [
        'min_bw'   => 'integer',
        'max_bw'   => 'integer',
        'eip_foc'  => 'integer',
        'anti_ddos'=> 'boolean',
    ];

    protected $attributes = [
    'anti_ddos' => false,
];


    public function logs()
    {
        return $this->hasMany(\App\Models\NetworkMappingLog::class);
       
    }
}
