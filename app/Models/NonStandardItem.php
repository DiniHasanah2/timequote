<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NonStandardItem extends Model
{
    use HasUuids;

    protected $fillable = [
           // Relationships
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',

        'item_name', 
        'unit',
        'quantity', 
        'cost', 
        'mark_up', 
        'selling_price'
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