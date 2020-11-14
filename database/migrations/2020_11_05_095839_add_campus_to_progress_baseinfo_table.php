<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampusToProgressBaseinfoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('progress_baseinfos', function (Blueprint $table) {
      $table->tinyInteger('campus')->default(1)->comment('校区');
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
