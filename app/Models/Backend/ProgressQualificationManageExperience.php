<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressQualificationManageExperience extends Model
{
  protected $table = 'progress_qualification_manage_experience';
  protected $fillable = [
    'user_id',
    'start_year',
    'start_month',
    'end_year',
    'end_month',
    'affairs',
    'prove_person',
    'order_sort',
  ];
}
