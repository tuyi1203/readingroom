<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PermissionController extends APIBaseController
{
  /**
   * 获取所有权限接口
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    //权限检查
    $this->checkPermission('list_permissions');

    $permissions = Permission::where('is_hide', 0)
      ->where('guard_name', $this->getGuardName($request))
      ->orderby('order_sort', 'desc')
      ->orderby('id', 'asc')
      ->get();

    //分层级组合权限列表
    $permissionTree = [];
    $this->getPermissionTree($permissions, $permissionTree);
    return $this->success($permissionTree);
  }

  private function getPermissionTree(&$permissions, &$root = [], &$parent = [])
  {
    foreach ($permissions as $i => $permission) {
      $data = [
        'id' => $permission['id'],
        'pid' => $permission['pid'],
        'name' => $permission['name'],
        'name_cn' => $permission['name_cn'],
        'order_sort' => $permission['order_sort'],
        'index' => $i,
      ];
      $rootCount = count($root);

      if (is_null($permission['pid']) && empty($parent)) {
        $root[$rootCount] = $data;
        unset($permissions[$i]);
        $this->getPermissionTree($permissions, $root, $root[$rootCount]);
      } else {
        if (!empty($parent)) {
          if ($permission['pid'] === $parent['id']) {
//            unset($permissions[$i]);
            $permissions[$i]['used'] = true;
            if (!isset($parent['child'])) {
              $parent['child'][0] = $data;
              $this->getPermissionTree($permissions, $root, $parent['child'][0]);
            } else {
              $cnt = count($parent['child']);
              $parent['child'][$cnt] = $data;
              $this->getPermissionTree($permissions, $root, $parent['child'][$cnt]);
            }
          }
        }
      }
    }
  }
}
