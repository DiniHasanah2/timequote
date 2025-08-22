<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientManager extends Model
{
    use HasFactory;

    protected $table = 'client_manager'; 

    public $incrementing = false; // using UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'staff_no',
        'division',
        'department',
        'email',
        'personal_contact',
    ];

   
}
