<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressBaseinfoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_baseinfos', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->string('name')->comment('姓名');
      $table->string('old_name')->comment('曾用名');
      $table->tinyInteger('min_zu')->comment('名族编号');
      $table->boolean('gender')->default(0)->comment('性别');
      $table->string('id_card')->comment('身份证号码');
      $table->string('company')->comment('工作单位');
      $table->tinyInteger('company_type')->comment('单位类别');
      $table->tinyInteger('apply_series')->comment('申报系列');
      $table->tinyInteger('apply_course')->comment('申报学科');
      $table->tinyInteger('had_position')->comment('现有职务级别');
      $table->tinyInteger('apply_position')->comment('申报职务级别');
      $table->tinyInteger('review_team_name')->comment('评审委员会名称');
      $table->string('graduate_school')->comment('最后毕业院校');
      $table->dateTime('graduate_time')->comment('最后毕业时间');
      $table->tinyInteger('education')->comment('最高学历');
      $table->string('education_no')->comment('学历证书号');
      $table->string('degree_no')->comment('学位证书号');
      $table->string('subject')->comment('专业');
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
    Schema::dropIfExists('progress_baseinfos');
  }
}
