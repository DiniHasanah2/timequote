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

    public function makeCurrent(): void
{
    \DB::transaction(function () {
        // Tutup current lama → jadi "Last"
        $old = self::where('is_current', true)->first();
        if ($old && $old->id !== $this->id) {
            $old->update([
                'is_current'   => false,
                'effective_to' => now()->toDateString(), // penting utk papar "Last"
            ]);
        }

        // Jadikan model ini current
        $this->is_current   = true;
        // kalau effective_from belum diisi, set hari ini
        if (empty($this->effective_from)) {
            $this->effective_from = now()->toDateString();
        }
        // current yang aktif tak patut ada effective_to
        $this->effective_to = null;
        $this->save();
    });
}

}
