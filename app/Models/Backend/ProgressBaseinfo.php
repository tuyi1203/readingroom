<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ProgressBaseinfo extends Model
{
  use Filterable;
  protected $table = 'progress_baseinfos';

  protected $fillable = [
    'name',
    'old_name',
    'min_zu',
    'gender',
    'id_card',
    'company',
    'company_type',
    'apply_series',
    'apply_course',
    'had_position',
    'apply_position',
    'review_team_name',
    'graduate_school',
    'graduate_time',
    'education',
    'education_no',
    'degree_no',
    'subject',
    'birthday',
    'zai_bian',
    'campus',
    'user_id'
  ];
}
