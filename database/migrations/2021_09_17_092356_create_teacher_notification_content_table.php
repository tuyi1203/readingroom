<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherNotificationContentTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('teacher_notification_content', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('plan_id')->index()->comment('通知计划的具体ID');
      $table->longText('content')->comment('通知内容附加数据');
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
    Schema::dropIfExists('teacher_notification_content');
  }
}
