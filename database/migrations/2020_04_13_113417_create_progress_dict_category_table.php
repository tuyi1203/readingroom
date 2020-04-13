<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressDictCategoryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_dict_categories', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('category_name')->comment('字典类别名称');
      $table->string('remark')->comment('字典类别描述');
      $table->integer('order_sort')->comment('字典类别排序');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('progress_dict_categories');
  }
}
