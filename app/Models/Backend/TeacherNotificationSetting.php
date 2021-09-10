<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class TeacherNotificationSetting extends Model
{
  use Filterable;

  protected $table = 'teacher_notification_settings';

  protected $fillable = [
    'user_id',
    'notification_type',
    'state',
  ];
}
