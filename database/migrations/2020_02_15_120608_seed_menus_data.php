<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;

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
        'name' => '用户管理',
        'permission_id' => 2,
        'url' => '/usermannager/list',
        'sort' => 1
      ]);

      Menu::create([
        'name' => '内容管理',
        'permission_id' => 1,
        'url' => '/content/list',
        'sort' => 2
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
