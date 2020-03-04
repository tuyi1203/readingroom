<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_infos', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->char('guid', 32)->unique()->comment('用户GUID');
      $table->string('full_name')->nullable()->comment('姓名');
      $table->string('nick_name')->nullable()->comment('昵称');
      $table->string('open_id')->nullable()->comment('微信OPENID');
      $table->string('union_id')->nullable()->comment('微信UNIONID');
      $table->string('mobile',20)->comment('手机号');
      $table->string('avatar')->nullable()->comment('头像地址');
      $table->boolean('gender')->nullable()->comment('性别');
      $table->string('address')->nullable()->comment('地址');
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
    Schema::dropIfExists('user_infos');
  }
}
