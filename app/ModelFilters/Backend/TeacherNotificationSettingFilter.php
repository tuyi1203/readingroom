<?php

namespace App\ModelFilters\Backend;

use EloquentFilter\ModelFilter;

class TeacherNotificationSettingFilter extends ModelFilter
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
}
