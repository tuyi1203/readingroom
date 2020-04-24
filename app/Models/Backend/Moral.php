<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class Moral extends Model
{
  protected $table = 'progress_morals';
  protected $fillable = [
    'user_id',
    'category',
    'summary',
    'kaohe',
    'warning',
    'punish',
    'niandu1',
    'niandu1_kaohe',
    'niandu2',
    'niandu2_kaohe',
    'niandu3',
    'niandu3kaohe',
    'niandu4',
    'niandu4_kaohe',
    'niandu5',
    'niandu5_kaohe',
  ];
}
