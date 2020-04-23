<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class FileConf extends Model
{
  protected $table = 'file_conf';
  protected $fillable = [
    'bize_type',
    'file_type_limit',
    'file_size_limit',
    'path',
    'description',
    'resource_realm',
    'enabled',
  ];
}
