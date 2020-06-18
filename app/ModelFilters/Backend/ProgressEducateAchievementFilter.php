<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressEducateAchievementFilter extends ModelFilter
{
  /**
   * Related Models that have ModelFilters as well as the method on the ModelFilter
   * As [relationMethod => [input_key1, input_key2]].
   *
   * @var array
   */
  public $relations = [];

  public function type($value)
  {
    return $this->where('type', $value);
  }

  public function awardDateFrom($value)
  {
    return $this->where('award_date', '>=', $value);
  }

  public function awardDateTo($value)
  {
    return $this->where('award_date', '<=', $value);
  }

  public function awardTitle($value)
  {
    return $this->whereLike('award_title', $value);
  }

  public function awardAuthoriryOrganization($value)
  {
    return $this->whereLike('award_authoriry_organization', $value);
  }

  public function lectureDateFrom($value)
  {
    return $this->where('lecture_date', '>=', $value);
  }

  public function lectureDateTo($value)
  {
    return $this->where('lecture_date', '<=', $value);
  }

  public function lectureContent($value)
  {
    return $this->whereLike('lecture_content', $value);
  }

  public function lectureOrganization($value)
  {
    return $this->whereLike('lecture_organization', $value);
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
