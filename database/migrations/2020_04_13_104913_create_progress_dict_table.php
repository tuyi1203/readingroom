<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressDictTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_dicts', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->integer('dict_code')->comment('字典ID');
      $table->string('dict_name')->comment('字典显示名');
      $table->string('dict_value')->comment('字典值');
      $table->string('remark')->comment('字典值描述');
      $table->integer('dict_category')->comment('字典类型');
      $table->integer('order_sort')->comment('排序');
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
    Schema::dropIfExists('progress_dicts');
  }
}
