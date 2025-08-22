<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ECSFlavour extends Model
{
    use HasFactory;

    protected $table = 'ecs_flavours'; 

    protected $fillable = [
    'ecs_code',
    'flavour_name',
    'vCPU',
    'RAM',
    'type',
    'generation',
    'memory_label',
    'windows_license_count',
    'red_hat_enterprise_license_count',
    'microsoft_sql_license_count',
    'dr',
    'pin',
    'gpu',
    'dedicated_host',
];

}