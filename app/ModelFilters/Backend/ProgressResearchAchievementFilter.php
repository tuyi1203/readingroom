<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressResearchAchievementFilter extends ModelFilter
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

  public function type($value)
  {
    return $this->where('type', $value);
  }

  public function paperTitle($value)
  {
    return $this->whereLike('paper_title', $value);
  }

  public function paperDateFrom($value)
  {
    return $this->where('paper_date', '>=', $value);
  }

  public function paperDateTo($value)
  {
    return $this->where('paper_date', '<=', $value);
  }

  public function award($value)
  {
    return $this->where('award', $value);
  }

  public function subjectTitle($value)
  {
    return $this->whereLike('subject_title', $value);
  }

  public function subjectResponseableMan($value)
  {
    return $this->whereLike('subject_responseable_man', $value);
  }

  public function subjectStatus($value)
  {
    return $this->where('subject_status', $value);
  }

  public function copyrightType($value)
  {
    return $this->whereLike('copyright_type', $value);
  }

  public function copyrightTitle($value)
  {
    return $this->whereLike('copyright_title', $value);
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
