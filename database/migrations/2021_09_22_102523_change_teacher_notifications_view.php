<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeTeacherNotificationsView extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::statement("CREATE OR REPLACE VIEW teacher_notifications_view AS
 SELECT a.id,a.user_id,a.plan_date, a.plan_time, a.plan_datetime, a.state AS plan_state, a.notification_type,
	     b.guid,b.full_name,b.nick_name,b.open_id, b.union_id, b.mobile, b.avatar,
       c.state
 FROM teacher_notification_plans a
 LEFT JOIN user_infos b ON a.user_id=b.user_id
 LEFT JOIN teacher_notification_settings c ON a.user_id=c.user_id AND a.notification_type=c.notification_type");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::statement("CREATE OR REPLACE VIEW teacher_notifications_view AS
 SELECT a.user_id,a.plan_date, a.plan_time, a.plan_datetime, a.state AS plan_state, a.notification_type,
	     b.guid,b.full_name,b.nick_name,b.open_id, b.union_id, b.mobile, b.avatar,
       c.state
 FROM teacher_notification_plans a
 LEFT JOIN user_infos b ON a.user_id=b.user_id
 LEFT JOIN teacher_notification_settings c ON a.user_id=c.user_id AND a.notification_type=c.notification_type");
  }
}
