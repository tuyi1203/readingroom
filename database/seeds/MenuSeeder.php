<?php

use App\Models\Backend\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $menu = new Menu();
    $menu->name = '常用功能';
    $menu->url = '#';
    $menu->sort = 0;
    $menu->save();

    $submenu = new Menu();
    $submenu->parent_id = $menu->id;
    $submenu->name = '待办提醒';
    $submenu->url = '/tools/notifications';
    $submenu->sort = 0;
    $submenu->save();

    $submenu = new Menu();
    $submenu->parent_id = $menu->id;
    $submenu->name = '课表管理';
    $submenu->url = '/tools/class_tables';
    $submenu->sort = 1;
    $submenu->save();

    $submenu = new Menu();
    $submenu->parent_id = $menu->id;
    $submenu->name = '值周表';
    $submenu->url = '/tools/weekly_tables';
    $submenu->sort = 2;
    $submenu->save();
  }
}
