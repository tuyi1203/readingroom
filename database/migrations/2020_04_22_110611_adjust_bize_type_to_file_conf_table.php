<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustBizeTypeToFileConfTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('file_conf', function (Blueprint $table) {
      $table->string('bize_type')->comment('业务类型，不同业务不同的类型')->unique()->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('file_conf', function (Blueprint $table) {
      $table->string('bize_type')->comment('业务类型，不同业务不同的类型')->change();
    });
  }
}
