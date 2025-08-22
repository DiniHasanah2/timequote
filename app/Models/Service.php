<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    public $incrementing = false; // <-- penting
    protected $keyType = 'string'; // <-- penting

    protected $fillable = [
        'id',
        'category_id',
        'category_name',
        'category_code',
        'code',
        'name',
        'measurement_unit',
        'description',
        'price_per_unit',
        'rate_card_price_per_unit',
        'transfer_price_per_unit',
        'created_at',
        'updated_at'
    ];

protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->id)) {
            $model->id = (string) Str::uuid();
        }
    });
}


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function prices()
{
    return $this->hasMany(\App\Models\ServicePrice::class);
}

public function priceForCatalog(?string $catalogId)
{
    return $this->prices()->when($catalogId, fn($q) => $q->where('price_catalog_id', $catalogId))->first();
}

}
