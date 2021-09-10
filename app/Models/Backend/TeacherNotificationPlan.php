<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class TeacherNotificationPlan extends Model
{
  use Filterable;

  protected $table = 'teacher_notification_plans';

  protected $fillable = [
    'user_id',
    'notification_type',
    'plan_date',
    'state',
  ];
}
