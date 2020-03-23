<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPidToPermissionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('permissions', function (Blueprint $table) {
      $table->integer('pid')->nullable()->default(null)->after('id')->commit('父级权限ID');
      $table->boolean('is_hide')->default(0)->commit('是否隐藏 1=隐藏 0=显示');
      $table->integer('order_sort')->default(0)->commit('排序位置');
      $table->string('name_cn')->after('name')->commit('中文名称');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('permissions', function (Blueprint $table) {
      $table->dropColumn('pid');
      $table->dropColumn('is_hide');
      $table->dropColumn('order_sort');
      $table->dropColumn('name_cn');
    });
  }
}
