<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ECSImport extends Model
{
    use HasUuids;
    protected $table = 'ecs_imports'; 
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['version_id', 'import_data'];
    protected $casts = [
        'import_data' => 'array',
    ];

    
}

