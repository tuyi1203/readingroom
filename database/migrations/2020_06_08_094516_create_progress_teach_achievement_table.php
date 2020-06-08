<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressTeachAchievementTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_teach_achievement', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->tinyInteger('achievement_type')->comment('成果类型');

      $table->date('award_date')->nullable()->comment('获奖时间');
      $table->string('award_main')->nullable()->comment('表彰主体(本人、本人所带班队、本人所带学生)');
      $table->string('award_title')->nullable()->comment('表彰奖励内容');
      $table->tinyInteger('award_type')->nullable()->comment('获奖类别');
      $table->tinyInteger('award_level')->nullable()->comment('获奖级别');
      $table->string('award_position')->nullable()->comment('获奖等次');
      $table->string('award_role')->nullable()->comment('本人作用');
      $table->string('award_authoriry_organization')->nullable()->comment('授奖单位');
      $table->string('award_authoriry_country')->nullable()->comment('授奖国家(地区）');

      $table->date('manage_exp_communicate_date')->nullable()->comment('交流管理经验时间');
      $table->string('manage_exp_communicate_content')->nullable()->comment('交流管理经验内容');
      $table->string('manage_exp_communicate_role')->nullable()->comment('本人作用');
      $table->string('manage_exp_communicate_range')->nullable()->comment('交流范围');

      $table->date('teacher_guide_date_start')->nullable()->comment('指导开始时间');
      $table->date('teacher_guide_date_end')->nullable()->comment('指导结束时间');
      $table->string('teacher_guide_name')->nullable()->comment('指导对象姓名');
      $table->string('teacher_guide_content')->nullable()->comment('指导内容');
      $table->string('teacher_guide_effect')->nullable()->comment('指导效果及荣誉和备注');

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
    Schema::dropIfExists('progress_teach_achievement');
  }
}
