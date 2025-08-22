<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VMMappings extends Model
{
    use HasFactory;

    protected $table = 'vm_mappings';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'vm_name',
        'customer_name',
        'project_id',
        'quotation_id',
        'ecs_flavour_mapping',
        'ecs_code',
    ];
}
