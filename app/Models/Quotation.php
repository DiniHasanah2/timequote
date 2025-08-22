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