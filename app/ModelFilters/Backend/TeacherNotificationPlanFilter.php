<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class TeacherNotificationPlanFilter extends ModelFilter
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

  public function notificationType($type)
  {
    return $this->where('notification_type', $type);
  }

  public function month($month)
  {
    $ts = strtotime($month.'-01 00:00:00');
    return $this->where('plan_datetime', '>=', date('Y-m-d 00:00:00', $ts))
      ->where('plan_datetime', '<=', date('Y-m-t 23:59:59', $ts));
  }
}
