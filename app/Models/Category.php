<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'category_code'];

    public $incrementing = false; // <-- penting sebab UUID bukan auto increment
    protected $keyType = 'string'; // <-- UUID guna string

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->id)) {
                $category->id = (string) Str::uuid();
            }
        });
    }
}
