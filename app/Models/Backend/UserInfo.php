<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
  protected $table = 'user_infos';
    //

  /**
   * 可以被批量赋值的属性。
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'guid',
    'open_id',
    'nick_name',
    'union_id',
    'mobile',
    'avatar',
    'gender',
    'address',
    'full_name',
  ];

  public function user()
  {
    return $this->belongsTo('App\Models\Backend\User');
  }
}
