<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressEducateAchievementTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_educate_achievement', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->tinyInteger('type')->comment('教学成果类型');
      $table->tinyInteger('achievement_type')->comment('教育教学科研成果类型');

      // 现场课、录像课、微课、课件、基本功
      $table->date('award_date')->nullable()->comment('获奖时间');
      $table->string('award_title')->nullable()->comment('表彰奖励内容');
      $table->tinyInteger('award_level')->nullable()->comment('获奖级别');
      $table->string('award_position')->nullable()->comment('获奖等次');
      $table->string('award_authoriry_organization')->nullable()->comment('授奖单位');
      $table->string('award_authoriry_country')->nullable()->comment('授奖国家(地区）');

      // 讲座、示范课
      $table->date('lecture_date')->nullable()->comment('讲座、示范课时间');
      $table->string('lecture_content')->nullable()->comment('讲座内容');
      $table->string('lecture_person')->nullable()->comment('主讲人');
      $table->string('lecture_organization')->nullable()->comment('主办单位');

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
    Schema::dropIfExists('progress_educate_achievement');
  }
}
