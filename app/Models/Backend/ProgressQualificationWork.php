<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressQualificationWork extends Model
{
  protected $tablename = 'progress_qualification_work';
  protected $fillable = [
    'work_time',
    'teach_years',
    'teach5years',
    'apply_up',
    'apply_course',
    'school_manager_title',
    'qualification_time',
    'work_first_time',
    'middle_school_time',
    'remark',
  ];
}
