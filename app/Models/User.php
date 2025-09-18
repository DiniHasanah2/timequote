<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    

    public $incrementing = false;
    protected $keyType = 'string';
    //protected $appends = ['role'];
    protected $fillable = [
        'id',
        'name',
        'username',
        'role',
        'email',
        'password',
        'role',
        'division',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            //'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->id = (string) Str::uuid();
            $base = Str::slug($user->name, '');
            $user->user_code = $base . Str::upper(Str::random(3));
            
            // Auto-hash password if not already hashed
            if (!empty($user->password) && !str_starts_with($user->password, '$2y$')) {
                $user->password = Hash::make($user->password);
            }
        });
    }

    

    // Relationship for customers (if exists)
    public function customers()
    {
        return $this->hasMany(Customer::class, 'presale_id');
    }

    public function assigned_projects()
{
    return $this->belongsToMany(Project::class, 'project_presale', 'presale_id', 'project_id');
}

}
