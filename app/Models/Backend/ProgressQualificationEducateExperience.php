<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressQualificationEducateExperience extends Model
{
  protected $table = 'progress_qualification_educate_experience';
  protected $fillable = [
    'user_id',
    'start_year',
    'start_month',
    'end_year',
    'end_month',
    'education',
    'school_name',
    'prove_person',
    'order_sort',
  ];
}
