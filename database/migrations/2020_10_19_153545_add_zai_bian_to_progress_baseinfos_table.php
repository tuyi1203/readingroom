<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZaiBianToProgressBaseinfosTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_baseinfos', function (Blueprint $table) {
      $table->tinyInteger('zai_bian')->default(0)->comment('在编/不在编')->after('gender');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('progress_baseinfos', function (Blueprint $table) {
      //
    });
  }
}
