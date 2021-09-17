<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class TeacherNotificationContent extends Model
{
    use Filterable;

    protected $table = 'teacher_notification_content';

    protected $fillable = [
      'plan_id',
      'content',
    ];
}
