<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressEducateBaseinfoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_educate_baseinfo', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->string('effect', 2000)->nullable()->comment('教学效果');
      $table->string('observe', 2000)->nullable()->comment('命题与监测');
      $table->string('communicate', 2000)->nullable()->comment('教研交流');
      $table->string('guide', 2000)->nullable()->comment('指导教师');
      $table->string('elective', 2000)->nullable()->comment('开设选修课或综合实践活动课');
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
    Schema::dropIfExists('progress_educate_baseinfo');
  }
}
