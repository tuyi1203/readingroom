<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class FileInfo extends Model
{
  protected $table = 'file_info';
  protected $fillable = [
    'bize_type',
    'bize_id',
    'original_name',
    'new_name',
    'file_type',
    'file_size',
    'file_path',
    'relative_path',
    'del_flg',
    'real_path',
    'user_id'
  ];
}
