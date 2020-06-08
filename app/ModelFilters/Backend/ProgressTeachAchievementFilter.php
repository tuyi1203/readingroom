<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressTeachAchievementFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function achievementType($value)
  {
    return $this->where('achievement_type', $value);
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
