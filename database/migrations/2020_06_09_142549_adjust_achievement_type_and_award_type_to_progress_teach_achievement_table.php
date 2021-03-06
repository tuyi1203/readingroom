<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustAchievementTypeAndAwardTypeToProgressTeachAchievementTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_teach_achievement', function (Blueprint $table) {
      $table->tinyInteger('type')->comment('科研成果类型');
      $table->string('achievement_type')->comment('教育教学科研成果类型')->change();
      $table->string('award_type')->nullable()->comment('获奖类别')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('progress_teach_achievement', function (Blueprint $table) {
      $table->dropColumn('type');
      $table->tinyInteger('achievement_type')->comment('科研成果类型')->change();
      $table->tinyInteger('award_type')->nullable()->comment('获奖类别')->change();
    });
  }
}
