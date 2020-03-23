<?php

namespace App\Models\Backend;

use Spatie\Permission\Models\Role;
use EloquentFilter\Filterable;

class ExtendRole extends Role
{
  use Filterable;

  protected $table = 'roles';

  protected $fillable = [
    'name',
    'name_zn',
    'guard_name',
    'order_sort',
  ];
}
