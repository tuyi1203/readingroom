<?php

namespace App\Models\Backend;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use EloquentFilter\Filterable;
use App\Models\Backend\UserInfo;

class User extends Authenticatable
{
  use Notifiable, HasApiTokens, hasRoles, Filterable;
  protected $table = 'users';
  protected $guard_name = 'backend';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'mobile',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * @return HasOne
   */
  public function userInfo()
  {
    return $this->hasOne(UserInfo::class, 'user_id', 'id');
  }

  protected static function boot()
  {
    parent::boot();

    static::deleting(function ($user) {
      $user->userInfo()->delete();
    });
  }

}
