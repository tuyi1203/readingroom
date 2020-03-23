<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZnnameToRolesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('roles', function (Blueprint $table) {
      $table->string('name_zn')->nullable()->after('name')->comment('角色中文名称');
      $table->integer('order_sort')->default(0)->commit('排序位置');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('roles', function (Blueprint $table) {
      $table->dropColumn('name_zn');
      $table->dropColumn('order_sort');
    });
  }
}
