<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'field_name',
        'old_value',
        'new_value',
        'user_id',
        'user_name',
        'action',
    ];

    protected $casts = [
        'old_value' => 'string',
        'new_value' => 'string',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
