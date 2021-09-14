<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class TeacherNotificationsView extends Model
{
  use Filterable;

  protected $table = 'teacher_notifications_view';

  protected $fillable = [];
}
