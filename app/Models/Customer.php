<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
class Customer extends Model
{
    use HasUuids; 

    protected $fillable = [
        'id',
        'division',
        'department',  
        'name', 
        'normalized_name',
        'business_number', 
        'presale_id',
        'presale_name',
        'client_manager_id',
        'client_manager',
        'created_by',
    ];


    protected static function booted()
    {
        static::creating(function ($customer) {
            if ($customer->presale_id) {
                $customer->presale_name = User::find($customer->presale_id)->name;
            }
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('presale_id')) {
                $customer->presale_name = User::find($customer->presale_id)->name;
            }
        });
    }


    // UUID Configuration 
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    // Relationships
public function clientManager()
{
    return $this->belongsTo(\App\Models\ClientManager::class, 'client_manager_id');
}

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function quotations()
    {
        return $this->hasManyThrough(
            \App\Models\Quotation::class,
            \App\Models\Project::class
        );
    }

    public function presale()
    {
        return $this->belongsTo(User::class, 'presale_id');
    }

    public function scopeOfDivision($query, ?string $division)
{
    return $division ? $query->where('division', $division) : $query;
}

   
}