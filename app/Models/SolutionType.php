<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Version;
use App\Models\User;

class SolutionType extends Model
{
    protected $fillable = [
        // Relationships
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',
        
        // Professional Services
        'solution_type',
        'production_region',
        'mpdraas_region',
        'dr_region',
       
    ];
    
   public function version()
{
    return $this->belongsTo(Version::class);
}

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function presale()
    {
        return $this->belongsTo(User::class, 'presale_id');
    }

}