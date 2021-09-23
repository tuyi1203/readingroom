<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherNotificationPlansTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('teacher_notification_plans', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->string('notification_type')->comment('通知类型');
      $table->date('plan_date')->comment('通知日期');
      $table->time('plan_time')->comment('通知时间');
      $table->dateTime('plan_datetime')->comment('日期时间');
      $table->boolean('state')->default(0)->comment('状态 1未发送 2进入队列 3处理队列 4完成发送');
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
    Schema::dropIfExists('teacher_notification_plans');
  }
}
