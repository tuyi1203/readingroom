<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuideAchievementToProgressAchievementTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_educate_achievement', function (Blueprint $table) {
      $table->string('award_main')->nullable()->comment('表彰主体(本人、本人所带班队、本人所带学生)');
      $table->string('award_role')->nullable()->comment('本人作用');
      $table->date('teacher_guide_date_start')->nullable()->comment('指导开始时间');
      $table->date('teacher_guide_date_end')->nullable()->comment('指导结束时间');
      $table->string('teacher_guide_name')->nullable()->comment('指导对象姓名');
      $table->string('teacher_guide_content')->nullable()->comment('指导内容');
      $table->string('teacher_guide_effect')->nullable()->comment('指导效果及荣誉和备注');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('progress_educate_achievement', function (Blueprint $table) {
      //
    });
  }
}
