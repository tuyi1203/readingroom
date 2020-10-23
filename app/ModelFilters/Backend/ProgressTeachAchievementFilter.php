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

  public function type($value)
  {
    return $this->where('type', $value);
  }

  public function user($id)
  {
    return $this->where('user_id', $id);
  }

  public function awardDateFrom($value)
  {
    return $this->where('award_date', '>=', $value);
  }

  public function awardDateTo($value)
  {
    return $this->where('award_date', '<=', $value);
  }

  public function manageExpCommunicateFrom($value)
  {
    return $this->where('manage_exp_communicate_date', '>=', $value);
  }

  public function manageExpCommunicateTo($value)
  {
    return $this->where('manage_exp_communicate_date', '<=', $value);
  }

  public function manageExpCommunicateContent($value)
  {
    return $this->whereLike('manage_exp_communicate_content', $value);
  }

  public function awardMain($value)
  {
    return $this->whereLike('award_main', $value);
  }

  public function teacherGuideName($value)
  {
    return $this->whereLike('teacher_guide_name', $value);
  }

  public function teacherGuideContent($value)
  {
    return $this->whereLike('teacher_guide_content', $value);
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
