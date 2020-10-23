<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class ProgressBaseinfoFilter extends ModelFilter
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

  public function ageFrom($ageFrom)
  {
    return $this->where(\DB::raw('TIMESTAMPDIFF(YEAR, birthday, CURDATE())'), '>=', $ageFrom);
  }

  public function ageTo($ageTo)
  {
    return $this->where(\DB::raw('TIMESTAMPDIFF(YEAR, birthday, CURDATE())'), '<=', $ageTo);
  }

//  public function gender($gender)
//  {
//    return $this->related('userInfo', 'gender', '=', $gender);
//  }

  public function gender($gender)
  {
    return $this->where('gender', $gender);
  }

  public function minZu($minZu)
  {
    return $this->where('min_zu', $minZu);
  }

  public function zaiBian($zaiBian)
  {
    return $this->where('zai_bian', $zaiBian);
  }

  public function applyPosition($applyPosition)
  {
    return $this->where('apply_position', $applyPosition);
  }

  public function hadPosition($hadPosition)
  {
    return $this->where('had_position', $hadPosition);
  }

  /*public function roles($rids)
  {
    if (!is_array($rids)) {
      $rids = [$rids];
    }
    return $this->whereHas('roles', function ($query) use ($rids) {
      $query->whereIn(config('permission.table_names.roles') . '.id', $rids);
    });
  }*/

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
