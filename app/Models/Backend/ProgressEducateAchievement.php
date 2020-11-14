<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ProgressEducateAchievement extends Model
{
  use Filterable;
  protected $table = 'progress_educate_achievement';
  protected $fillable = [
    'user_id',
    'type',
    'achievement_type',
    'award_type',
    'award_date',
    'award_title',
    'award_level',
    'award_role',
    'award_main',
    'award_position',
    'award_authoriry_organization',
    'award_authoriry_country',
    'lecture_date',
    'lecture_content',
    'lecture_person',
    'lecture_organization',
    'lecture_scope',
    'teacher_guide_date_start',
    'teacher_guide_date_end',
    'teacher_guide_name',
    'teacher_guide_content',
    'teacher_guide_effect',
  ];
}
