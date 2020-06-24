<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuickMenusToUserInfosTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('user_infos', function (Blueprint $table) {
      $table->string('quick_menus')->nullable()->comment('快捷菜单');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('user_infos', function (Blueprint $table) {
      $table->dropColumn('quick_menus');
    });
  }
}
