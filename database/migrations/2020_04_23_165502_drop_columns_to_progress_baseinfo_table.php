<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsToProgressBaseinfoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_baseinfos', function (Blueprint $table) {
      $table->dropColumn('graduate_school');
      $table->dropColumn('graduate_time');
      $table->dropColumn('education');
      $table->dropColumn('education_no');
      $table->dropColumn('degree_no');
      $table->dropColumn('subject');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('progress_baseinfos', function (Blueprint $table) {
      $table->string('graduate_school')->comment('最后毕业院校');
      $table->dateTime('graduate_time')->comment('最后毕业时间');
      $table->tinyInteger('education')->comment('最高学历');
      $table->string('education_no')->comment('学历证书号');
      $table->string('degree_no')->comment('学位证书号');
      $table->string('subject')->comment('专业');
    });
  }
}
