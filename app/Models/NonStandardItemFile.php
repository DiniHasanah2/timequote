<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NonStandardItemFile extends Model
{
    protected $table = 'non_standard_item_files';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','project_id','version_id','customer_id',
        'original_name','stored_path','mime_type','size_bytes','ext'
    ];

    protected static function booted() {
        static::creating(function ($m) {
            if (!$m->id) $m->id = (string) Str::uuid();
        });
    }

    public function version(){ return $this->belongsTo(Version::class); }
    public function project(){ return $this->belongsTo(Project::class); }
}
