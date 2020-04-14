<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustOrderSortToProgressDictCategoryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_dict_categories', function (Blueprint $table) {
      $table->integer('order_sort')->default(0)->comment('字典类别排序')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('progress_dict_categories', function (Blueprint $table) {
      $table->integer('order_sort')->default(null)->comment('字典类别排序')->change();
    });
  }
}
