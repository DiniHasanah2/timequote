<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ServicePrice extends Model
{
    use HasUuids;

    protected $fillable = [
        'price_catalog_id', 'service_id',
        'price_per_unit', 'rate_card_price_per_unit', 'transfer_price_per_unit'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function catalog()
    {
        return $this->belongsTo(PriceCatalog::class, 'price_catalog_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
