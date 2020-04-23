<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustBizeIdToFileInfoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('file_info', function (Blueprint $table) {
      $table->bigInteger('bize_id')->comment('业务ID')->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('file_info', function (Blueprint $table) {
      $table->bigInteger('bize_id')->comment('业务ID')->change();
    });
  }
}
