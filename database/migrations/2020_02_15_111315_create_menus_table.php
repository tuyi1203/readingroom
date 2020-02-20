<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    $permissionTableNames = config('permission.table_names');

    Schema::create('menus', function (Blueprint $table) use ($permissionTableNames) {
      $table->bigIncrements('id');
      $table->string('name')->default('')->commit('菜单名称');
      $table->string('icon')->default('')->commit('菜单图标')->nullable();
      $table->unsignedBigInteger('parent_id')->comment('父级菜单ID')->nullable();
      $table->unsignedBigInteger('permission_id')->comment('权限ID')->nullable();
      $table->string('url')->default('')->comment('菜单链接')->nullable();
//      $table->string('heightlight_url')->default('')->comment('菜单高亮');
      $table->tinyInteger('sort')->unsigned()->default(0)->comment('排序');
      $table->timestamps();

      $table->foreign('permission_id')
        ->references('id')
        ->on($permissionTableNames['permissions'])
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('menus');
  }
}
