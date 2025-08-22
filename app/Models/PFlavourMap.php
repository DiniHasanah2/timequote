<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PFlavourMap extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }

    protected $fillable = [
        'flavour', 'vcpu', 'vram', 'type', 'generation',
        'memory_label', 'windows_license_count', 'rhel',
        'dr', 'pin', 'gpu', 'ddh', 'mssql'
    ];
}
