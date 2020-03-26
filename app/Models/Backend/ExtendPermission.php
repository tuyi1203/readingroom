<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Spatie\Permission\Models\Permission;

class ExtendPermission extends Permission
{
  use Filterable;

  protected $table = 'permissions';

  protected $fillable = [
    'pid',
    'name',
    'name_zn',
    'guard_name',
    'is_hide',
    'order_sort'
  ];
}
