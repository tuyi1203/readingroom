<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyTokenTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('third_party_tokens', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('third_party_name')->comment('三方平台名称');
      $table->string('token_type')->comment('token的类型');
      $table->string('access_token',1000)->comment('token字符');
      $table->dateTime('expire_at')->comment('过期时间');
//      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('third_party_tokens');
  }
}
