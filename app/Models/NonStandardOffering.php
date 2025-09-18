<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NonStandardOffering extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_id','version_id','customer_id','presale_id',
        'category_id','category_name','category_code',
        'service_id','service_name','service_code',
        'unit','quantity','months',
        'unit_price_per_month','mark_up','selling_price',
        'source_non_standard_item_id',
    ];

    protected $casts = [
    'months' => 'integer',
];


    public function version(){ return $this->belongsTo(Version::class); }
    public function project(){ return $this->belongsTo(Project::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function presale(){ return $this->belongsTo(User::class,'presale_id'); }

    public function category(){ return $this->belongsTo(Category::class); }
    public function service(){ return $this->belongsTo(Service::class); }
    public function sourceItem(){ return $this->belongsTo(NonStandardItem::class, 'source_non_standard_item_id'); }
}
