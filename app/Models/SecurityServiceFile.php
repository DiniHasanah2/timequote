<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SecurityServiceFile extends Model
{
    protected $table = 'security_service_files';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'version_id',
        'project_id',
        'customer_id',
        'original_name',
        'stored_path',
        'mime_type',
        'ext',
        'size_bytes',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            if (empty($m->id)) $m->id = (string) Str::uuid();
        });
    }
}
