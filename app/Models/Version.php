<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Version extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'project_id',
        'version_name',
        'version_number'
    ];



    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }


public function solution_type()
{
    return $this->hasOne(SolutionType::class);
}

public function region()
{
    return $this->hasOne(Region::class, 'version_id', 'id');
}


public function mpdraas()
{
    return $this->hasOne(MPDRaaS::class);
}
 public function security_service()
{
    return $this->hasOne(SecurityService::class);
}

public function ecs_configuration()
{
    //return $this->hasMany(ECSConfiguration::class);
    return $this->hasMany(\App\Models\ECSConfiguration::class);
}


public function backup()
{
    //return $this->hasMany(ECSConfiguration::class);
     return $this->hasMany(\App\Models\ECSConfiguration::class);
}


 public function non_standard_items()
{
      return $this->hasMany(NonStandardItem::class);
}

public function internal_summary()
{
    return $this->hasOne(InternalSummary::class, 'version_id', 'id');
}


// Di method controller
public function showInternalSummary($versionId)
{
    $version = Version::with([
                'project', 
                'project.customer', 
                'project.presale'
              ])->findOrFail($versionId);
    
    return view('projects.security_service.internal_summary', [
        'version' => $version,
        'project' => $version->project
    ]);
}



    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
  



    

    protected static function booted()
{
    static::creating(function ($version) {
        // Get latest version for this project
        $latest = Version::where('project_id', $version->project_id)
            ->orderBy('version_number', 'desc')
            ->first();
        
        // If no versions exist, start with 1.0
        if (!$latest) {
            $version->version_number = '1.0';
            return;
        }


        

        // Increment version number
        $parts = explode('.', $latest->version_number);
        $version->version_number = $parts[0] . '.' . ($parts[1] + 1);
    });

        // ADD THIS NEW EVENT LISTENER FOR CREATED
        static::created(function ($version) {
            \Log::info("New version created", [
                'id' => $version->id,
                'project_id' => $version->project_id,
                'version_number' => $version->version_number
            ]);
        });
    }
    // app/Models/Version.php

public function latestQuotation()
{
    // uses updated_at to decide "latest"
    return $this->hasOne(\App\Models\Quotation::class)->latestOfMany('updated_at');
}


}
