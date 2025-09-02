<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quotation extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'version_id',
        'quote_code',
        'total_amount',
        'status',
        'presale_id',
    ];

    



    protected static function booted()
{
    static::creating(function ($m) {
        if (empty($m->id)) {
            $m->id = (string) \Illuminate\Support\Str::uuid(); // UUID => lowercase + dash
        }
    });
}


    public function version()   { return $this->belongsTo(Version::class, 'version_id'); }
    public function presale()   { return $this->belongsTo(User::class, 'presale_id'); }
    public function project()   { return $this->belongsTo(Project::class); }
    public function products()  { return $this->hasMany(Product::class); }


    // app/Models/Quotation.php

public function getDisplayTotalAttribute()
{
    // prefer a stored final total if you have it
    if (isset($this->final_total) && $this->final_total > 0) {
        return round((float) $this->final_total, 2);
    }

    // else: try to compute final = contract_total × (1 + sst_rate)
    $sstRate = isset($this->sst_rate) ? (float) $this->sst_rate : 8.0;

    if (isset($this->contract_total) && $this->contract_total > 0) {
        return round((float) $this->contract_total * (1 + $sstRate/100), 2);
    }

    // last fallback: total_amount × (1 + sst_rate) OR just total_amount
    if (isset($this->total_amount) && $this->total_amount > 0) {
        return round((float) $this->total_amount * (1 + $sstRate/100), 2);
    }

    return 0.0;
}

}

/*namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
     protected $keyType = 'string';
    public $incrementing = false;  
    protected $fillable = [
        'id',
        'project_id',        
        'version_id', 
        'quote_code',
        'total_amount',
        'status',
        'presale_id',
        'created_at',
        'updated_at'
    ];

    public function version()
    {
        return $this->belongsTo(Version::class, 'version_id');
        //return $this->belongsTo(Version::class);
    }
    

    public function presale()
    {
        return $this->belongsTo(User::class, 'presale_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function products()
{
    return $this->hasMany(Product::class);
}

}*/