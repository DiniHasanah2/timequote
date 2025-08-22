<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'project_name',
        'total_service_category',
        'total_services',
        'highest_quoted_service'
    ];

    // app/Models/Product.php



/*public function customer()
{
    return $this->belongsTo(Customer::class);
}*/


    public function presale()
    {
        return $this->belongsTo(User::class, 'presale_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

     public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function service()
{
    return $this->belongsTo(Service::class, 'services_id');
}

 public function ecs_flavour()
    {
        return $this->belongsTo(ECSFlavour::class);
    }

     public function network_mapping()
    {
        return $this->belongsTo(NetworkMapping::class);
    }


public function quotation()
{
    return $this->belongsTo(Quotation::class, 'quotation_id');
}



}