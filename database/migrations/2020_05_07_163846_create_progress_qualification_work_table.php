<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressQualificationWorkTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_qualification_work', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->date('work_time')->nullable()->comment('参加工作时间');
      $table->tinyInteger('teach_years')->nullable()->comment('教龄');
      $table->boolean('teach5years')->default(0)->comment('是否在一级教师岗位任教满5年及以上');
      $table->boolean('apply_up')->default(0)->comment('是否申请破格须在一级教师岗位任教至少3年');
      $table->tinyInteger('apply_course')->nullable()->comment('申报学科/从事专业');
      $table->boolean('school_manager')->nullable()->comment('是否校级管理人员');
      $table->string('title')->nullable()->comment('现任专业技术职务');
      $table->date('qualification_time')->nullable()->comment('资格取得时间');
      $table->date('work_first_time')->nullable()->comment('现任专业技术职务首聘时间');
      $table->boolean('middle_school_teacher')->default(0)->nullable()->comment('是否为中学特级教师');
      $table->date('middle_school_time')->nullable()->comment('中级特级教师取得时间');
      $table->string('remark', 1000)->nullable()->comment('参加何社会、学术团体、任何职务');
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
    Schema::dropIfExists('progress_qualification_work');
  }
}
