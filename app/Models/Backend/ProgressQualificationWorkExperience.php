<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressQualificationWorkExperience extends Model
{
  protected $tablename = 'progress_qualification_work_experience';
  protected $fillable = [
    'start_year',
    'start_month',
    'end_year',
    'end_month',
    'company',
    'affairs',
    'prove_person',
    'order_sort',
  ];
}
