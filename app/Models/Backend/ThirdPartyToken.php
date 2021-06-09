<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyToken extends Model
{
  protected $table = 'third_party_tokens';

  public $timestamps = false;

  protected $fillable = [
    'third_party_name',
    'token_type',
    'access_token',
    'expire_at',
  ];
}
