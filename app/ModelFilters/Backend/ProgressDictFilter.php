<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressDictFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function categoryName($name)
  {
    return $this->related('category', function ($query) use ($name) {
      return $query->where('category_name', $name);
    });
  }

  public function order($value)
  {
    switch ($value) {
      default:
        $this->orderBy('order_sort', 'desc')
          ->orderBy('id', 'desc');
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
