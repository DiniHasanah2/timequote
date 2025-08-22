<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PriceCatalog extends Model
{
    use HasUuids;

    protected $fillable = [
            'version_code', 'version_name', 'effective_from', 'effective_to', 'is_current', 'notes',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function servicePrices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function scopeCurrent($q)
    {
        return $q->where('is_current', true);
    }

    // 👉 tambah ni
    public function makeCurrent(): void
    {
        static::where('is_current', true)->update(['is_current' => false]);
        $this->is_current = true;
        $this->save();
    }
}
