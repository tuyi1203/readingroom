<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressAwardAchievementFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function user($id)
  {
    return $this->where('user_id', $id);
  }

  public function awardTitle($title)
  {
    return $this->where('award_title', $title);
  }

  public function awardDateFrom($value)
  {
    return $this->where('award_date', '>=', $value);
  }

  public function awardDateTo($value)
  {
    return $this->where('award_date', '<=', $value);
  }

  public function awardAuthoriryOrganization($organization)
  {
    return $this->whereLike('award_authoriry_organization', $organization);
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
