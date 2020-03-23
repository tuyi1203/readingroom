<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class RoleFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function nameZn($value)
  {
    return $this->whereLike('name_zn', $value);
  }

  public function name($value)
  {
    return $this->whereLike('name', $value);
  }

  public function guardName($value)
  {
    return $this->where('guard_name', $value);
  }

  public function order($value)
  {
    switch ($value) {
      case 'asc':
        $this->orderBy('id', 'asc');
        break;
      default:
        $this->orderBy('order_sort', 'desc')
          ->orderBy('id', 'desc')
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
