<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{


     protected $fillable = [
        'project_id',
        'version_id',
        'customer_id',
        'presale_id',
        'status',
        'quotation_id',
        'project_name',
        'version_name',
        'customer_name',
    ];
public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id', 'id'); 
}


public function project()
{
    return $this->belongsTo(Project::class);
}

public function version()
{
    return $this->belongsTo(Version::class);
}


public function quotation()
{
    return $this->belongsTo(\App\Models\Quotation::class, 'quotation_id', 'id');
}








}
