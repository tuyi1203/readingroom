<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Backend\Menu;

class SeedMenusData extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Menu::create([
      'id' => 1,
      'name' => '站点管理',
      'sort' => 0
    ]);
    Menu::create([
      'id' => 2,
      'name' => '人员及权限管理',
      'parent_id' => 1,
      'sort' => 1
    ]);
    Menu::create([
      'id' => 3,
      'name' => '人员管理',
//      'permission_id' => 2,
      'parent_id' => 2,
      'url' => '/usermanager/user',
      'sort' => 2
    ]);
    Menu::create([
      'id' => 4,
      'name' => '角色管理',
//      'permission_id' => 3,
      'parent_id' => 2,
      'url' => '/usermanager/role',
      'sort' => 2
    ]);

    Menu::create([
      'name' => '预定管理',
      'url' => '',
      'sort' => 1
    ]);
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::table('menus')->delete();
  }
}
