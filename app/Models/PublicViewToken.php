<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PublicViewToken extends Model {
  protected $fillable = [
    'quote_id','version_id','is_latest',
    'token','token_hash',
    'expires_at','revoked_at','created_by'
  ];

  protected $casts = [
    'is_latest'=>'bool',
    'expires_at'=>'datetime',
    'revoked_at'=>'datetime',
  ];
}
