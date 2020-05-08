<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressQualificationEducateExperience extends Model
{
  protected $tablename = 'progress_qualification_educate_experience';
  protected $fillable = [
    'start_year',
    'start_month',
    'end_year',
    'end_month',
    'education',
    'prove_person',
    'order_sort',
  ];
}
