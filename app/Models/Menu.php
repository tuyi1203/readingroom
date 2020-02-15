<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
  protected $table = 'menus';

  protected $fillable = [
    'name',
    'icon',
    'parent_id',
    'permission_id',
    'url',
    'sort'
  ];
}
