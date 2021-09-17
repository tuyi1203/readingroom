<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeacherNotificationPlan extends Model
{
  use Filterable;

  protected $table = 'teacher_notification_plans';

  protected $fillable = [
    'user_id',
    'notification_type',
    'plan_date',
    'plan_time',
    'plan_datetime',
    'state',
  ];

  public function content(): HasOne
  {
    return $this->hasOne('App\Models\Backend\TeacherNotificationContent', 'plan_id');
  }
}
