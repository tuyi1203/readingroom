<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class ProgressDict extends Model
{
  protected $table = 'progress_dicts';
  protected $fillable = [
    'dict_code',
    'dict_value',
    'dict_name',
    'remark',
    'dict_category',
    'order_sort'
  ];

  public function category()
  {
    return $this->belongsTo(ProgressDictCategory::class, 'dict_category', 'id');
  }

}
