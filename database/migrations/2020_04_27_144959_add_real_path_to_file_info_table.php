<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRealPathToFileInfoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('file_info', function (Blueprint $table) {
      $table->string('real_path')->comment('文件实际路径');
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
      $table->dropColumn('real_path');
    });
  }
}
