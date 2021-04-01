<?php

namespace App\Models\Backend;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ProgressDictCategory extends Model
{
  use Filterable;

  protected $table = 'progress_dict_categories';
}
