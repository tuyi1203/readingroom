<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function name($name)
  {
    return $this->whereLike('name', $name);
  }

  public function isActive($isActive)
  {
    return $this->where('is_active', $isActive);
  }

  public function gender($gender)
  {
    return $this->related('userInfo', 'gender', '=', $gender);
  }

  public function email($email)
  {
    return $this->whereLike('email', $email);
  }

  public function roles($rids)
  {
    if (!is_array($rids)) {
      $rids = [$rids];
    }
    return $this->whereHas('roles', function ($query) use ($rids) {
      $query->whereIn(config('permission.table_names.roles') . '.id', $rids);
    });
  }

  public function order($value)
  {
    switch ($value) {
      case 'asc':
        $this->orderBy('id', 'asc');
        break;
      default:
        $this->orderBy('id', 'asc')
          ->orderBy('created_at', 'desc');
        break;
    }
  }

  public function setup()
  {
    // 如果没有传 order，则默认使用 default
    if (!$this->input('order')) {
      $this->push('order', 'default');
    }
  }
}
