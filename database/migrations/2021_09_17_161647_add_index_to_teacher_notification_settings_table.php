<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTeacherNotificationSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('teacher_notification_settings', function (Blueprint $table) {
      $table->unique(['user_id', 'notification_type'], 'uni_user_notification_type');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('teacher_notification_settings', function (Blueprint $table) {
      $table->dropUnique('uni_user_notification_type');
    });
  }
}
