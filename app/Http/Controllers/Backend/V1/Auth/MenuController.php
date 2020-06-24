<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Models\Backend\UserInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MenuController extends APIBaseController
{
  /**
   * 获取用户快捷菜单接口
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $userInfos = UserInfo::where('user_id', $this->user->id)->firstOrFail(['user_id', 'quick_menus']);
    $menus = $userInfos->toArray()['quick_menus'];
    return $this->success(explode(',', $menus));
  }

  /**
   * 新增用户快捷菜单接口
   * @param Request $request
   * @param $menuId
   * @return JsonResponse
   */
  public function store(Request $request)
  {

    $userInfos = UserInfo::where('user_id', $this->user->id)->firstOrFail(['user_id', 'quick_menus']);
    $menus = $userInfos->toArray()['quick_menus'];
    if (empty($menus)) {
      $menus = $request->menu_id;
    } else {
      $menus .= ',' . $request->menu_id;
    }
    $boolResult = UserInfo::where('user_id', $this->user->id)->update([
      'quick_menus' => $menus
    ]);

    return $this->success($request->menu_id, 'Add succeed.');
  }

  /**
   * 删除用户快捷菜单
   * @param Request $request
   * @param $menuId
   * @return JsonResponse
   */
  public function destroy(Request $request, $menuId)
  {

    $userInfo = UserInfo::where('user_id', $this->user->id)->firstOrFail();
    $menus = $userInfo->toArray()['quick_menus'];
    $arrMenus = explode(',', $menus);
    array_splice($arrMenus, array_search($menuId, $arrMenus), 1);
    $boolResult = UserInfo::where('user_id', $this->user->id)->update([
      'quick_menus' => implode(',', $arrMenus),
    ]);

    return $this->success($boolResult, 'Delete succeed.');
  }
}
