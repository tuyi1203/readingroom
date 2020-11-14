<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLectureScopeToProgressEducateAchievementTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_educate_achievement', function (Blueprint $table) {
      $table->text('lecture_scope')->nullable()->comment('讲座发表范围')->after('lecture_organization');
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
