<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustAchievementTypeToProgressEducateAchievementTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_educate_achievement', function (Blueprint $table) {
      $table->string('achievement_type')->comment('教育教学科研成果类型')->change();
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
