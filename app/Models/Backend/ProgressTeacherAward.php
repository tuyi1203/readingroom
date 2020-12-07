<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class ProgressTeacherAward extends Model
{
  use Filterable;
  protected $table = 'award_achievement_view';
}
