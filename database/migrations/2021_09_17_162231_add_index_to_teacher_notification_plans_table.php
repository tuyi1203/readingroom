<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTeacherNotificationPlansTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('teacher_notification_plans', function (Blueprint $table) {
      $table->index(['user_id', 'notification_type'], 'idx_user_notification_type');
      $table->index(['plan_datetime', 'state'], 'idx_plan_datetime_state');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('teacher_notification_plans', function (Blueprint $table) {
      $table->dropIndex('idx_user_notification_type');
      $table->dropIndex('idx_plan_datetime_state');
    });
  }
}
