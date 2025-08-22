<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_id',
        'presale_id',
        'name',
        'quotation_value',
        'customer_name',
    ];

    protected static function booted()
{
    static::deleting(function ($project) {
        $project->versions()->delete();
    });
}

public function presale()
{
    return $this->belongsTo(User::class, 'presale_id');
}

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function versions()
{
    return $this->hasMany(Version::class);
}

    // Accessor for customer_name
public function getCustomerNameAttribute()
{
    return $this->customer ? $this->customer->name : 'N/A';
}

// Accessor for latest version name
public function getLatestVersionNameAttribute()
{
    return $this->versions->first()->version_name ?? 'N/A';
}

// Accessor for latest version number
public function getLatestVersionNumberAttribute()
{
    return $this->versions->first()->version_number ?? 'N/A';
}

public function solution_type()
{
    return $this->hasManyThrough(SolutionType::class, Version::class);
}

public function regions()
{
    return $this->hasManyThrough(Region::class, Version::class);
}

public function mpdraas()
{
    return $this->hasManyThrough(MPDRaaS::class, Version::class);
}

public function security_services()
{
    return $this->hasManyThrough(SecurityService::class, Version::class);
}

public function ecs_configuration()
{
    return $this->hasMany(ECSConfiguration::class);
}

public function backup()
{
    return $this->hasMany(ECSConfiguration::class);
}


public function non_standard_items()
{
    return $this->hasManyThrough(NonStandardItem::class, Version::class);
}


public function assigned_presales()
{
    return $this->belongsToMany(User::class, 'project_presale', 'project_id', 'presale_id');
}


public function quotations()
{
    return $this->hasManyThrough(
        Quotation::class,
        Version::class,
        'project_id', // Foreign key on Version table
        'version_id', // Foreign key on Quotation table
        'id', // Local key on Project table
        'id' // Local key on Version table
    );
}

// Accessor to get total quotation value
public function getTotalQuotationValueAttribute()
{
    return $this->quotations->sum('total_amount');
}



}
