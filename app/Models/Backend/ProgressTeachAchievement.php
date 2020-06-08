<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ProgressTeachAchievement extends Model
{
  use Filterable;
  protected $table = 'progress_teach_achievement';

  protected $fillable = [
    'user_id',
    'achievement_type',
    'award_date',
    'award_main',
    'award_title',
    'award_type',
    'award_level',
    'award_position',
    'award_role',
    'award_authoriry_organization',
    'award_authoriry_country',
    'manage_exp_communicate_date',
    'manage_exp_communicate_content',
    'manage_exp_communicate_role',
    'manage_exp_communicate_range',
    'teacher_guide_date_start',
    'teacher_guide_date_end',
    'teacher_guide_name',
    'teacher_guide_content',
    'teacher_guide_effect',
  ];
}
