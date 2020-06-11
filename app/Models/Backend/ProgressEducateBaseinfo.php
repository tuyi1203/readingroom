<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressEducateBaseinfo extends Model
{
  protected $table = 'progress_educate_baseinfo';
  protected $fillable = [
    'user_id',
    'effect',
    'observe',
    'communicate',
    'guide',
    'elective',
  ];
}
