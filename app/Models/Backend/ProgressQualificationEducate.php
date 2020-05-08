<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressQualificationEducate extends Model
{
  protected $table = 'progress_qualification_educate';
  protected $fillable = [
    'graduate_school',
    'graduate_time',
    'education',
    'education_no',
    'degree_no',
    'subject',
    'manage_years',
    'rural_teach_years',
  ];
}
