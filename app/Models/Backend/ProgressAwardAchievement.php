<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ProgressAwardAchievement extends Model
{
  use Filterable;
  protected $table = 'progress_award_achievement';
  protected $fillable = [
    'user_id',
    'type',
    'achievement_type',
    'award_type',
    'award_date',
    'award_title',
    'award_level',
    'award_remark',
    'award_position',
    'award_authoriry_organization',
    'award_authoriry_country',
  ];
}
