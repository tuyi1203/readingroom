<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressTeacherAwardFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function campus($campus)
  {
    return $this->where('campus', $campus);
  }

  public function awardOrAchievement($which)
  {
    return $this->whereLike('award_or_achievement', $which);
  }

  public function dateFrom($value)
  {
    return $this->where('the_date', '>=', $value);
  }

  public function dateTo($value)
  {
    return $this->where('the_date', '<=', $value);
  }

  public function userName($teacherName)
  {
    return $this->whereLike('user_name', $teacherName);
  }

  public function order($value)
  {
    switch ($value) {
      default:
        $this->orderBy('id', 'asc');
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
