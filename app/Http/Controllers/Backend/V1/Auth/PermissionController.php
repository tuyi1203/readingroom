<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use App\Models\Backend\ExtendRole as Role;
use App\Models\Backend\ExtendPermission as Permission;
use Illuminate\Http\Request;
use App\ModelFilters\Backend\PermissionFilter;

class PermissionController extends APIBaseController
{
  /**
   * 获取权限列表接口
   * @param Request $request
   * @param null $id
   * @return JsonResponse
   */
  public function index(Request $request, $id = null)
  {
    //权限检查
    $this->checkPermission('list_permissions');

    //如果传递了roleid参数，则取出所有该角色拥有的权限

    if ($request->route('rid') && $id) {
      $role = Role::where('id', $id)->firstOrFail();

      $permissions = $role->getAllPermissions();

      return $this->success($permissions);
    }

    //如果传递了pid参数，则取出所有子权限
    if ($request->route('pid') && $id) {

      $permissions = Permission::filter($this->getParams($request, [
        'pid' => $id,
      ]), PermissionFilter::class)
        ->paginate($this->getPageSize($request), [
          'id',
          'pid',
          'name',
          'name_cn'
        ], 'page', $this->getCurrentPage($request))
        ->toArray();

      return $this->success($permissions);
    }

    $permissions = Permission::filter($this->getParams($request),
      PermissionFilter::class)
      ->paginate($this->getPageSize($request), [
        'id',
        'pid',
        'name',
        'name_cn'
      ], 'page', $this->getCurrentPage($request))
      ->toArray();

    //分层级组合权限列表
    /*
    $permissionTree = [];
    $this->getPermissionTree($permissions, $permissionTree);
    */

    return $this->success($permissions);
  }

  /**
   * 保存修改角色权限
   * @param Request $request
   * @return JsonResponse
   */
  public function update(Request $request, $id = null)
  {

  }

  /**
   * 更新角色拥有的权限列表
   * @param Request $request
   * @param null $rid
   * @return JsonResponse
   */
  public function updateRolePermissions(Request $request, $rid = null)
  {

    $this->checkPermission('save_permissions');

    if (!$rid) {
      return $this->failed('Wrong request!');
    }

    $role = Role::where('id', $rid)->firstOrFail();

    //获取权限的添加修改关系数组
    $permissionsNeedToHandle = $this->diffPermissions($request, $role);
//    return $this->success($permissionsNeedToHandle);
    if (count($permissionsNeedToHandle)) {
      foreach ($permissionsNeedToHandle as $permission) {
        if ($permission['action'] == 'add') {
          $role->givePermissionTo($permission['name']);
        }
        if ($permission['action'] == 'delete') {
          $role->revokePermissionTo($permission['name']);
        }
      }
    }

    return $this->success($role);
  }


  /**
   * 对比权限数组，获取权限的添加修改关系
   * @param Request $request
   * @param $role 角色
   * @return array
   */
  private function diffPermissions(Request $request, $role)
  {
    //取得全部可能的权限
    $permissions = Permission::where('guard_name',
      $this->getGuardName($request))->where('is_hide', 0)->get()->toArray();

    //取得该角色的所有权限
    $roleHasPermissions = $role->getAllPermissions($request)->toArray();

    //取得用户提交的权限列表
    $submitPermissions = Permission::filter($this->getParams($request),
      PermissionFilter::class)->get()->toArray();

    $needUpdateRoles = [];
    foreach ($permissions as $permission) {
      $temPermission = [];
      $inRoleHas = $inSubmit = false;
      if (array_search($permission['id'],
          array_column($roleHasPermissions, 'id'), true) !== false) {
        $temPermission = [
          'name' => $permission['name'],
        ];
        $inRoleHas = true;
      }

      if (array_search($permission['id'],
          array_column($submitPermissions, 'id'), true) !== false) {
        if (count($temPermission) < 1) {
          $temPermission = [
            'name' => $permission['name'],
          ];
        }
        $inSubmit = true;
      }

      if (count($temPermission) > 0) {
        //原有权限中没有，但是提交有该权限时，设置flg为添加
        if ($inSubmit && !$inRoleHas) {
          $temPermission['action'] = 'add';
        }
        //原有权限没有，提交也有该权限时，设置flg为忽略
        if ($inSubmit && $inRoleHas) {
          $temPermission['action'] = 'ignore';
        }
        //原有权限有，提交没有该权限时，设置flg为删除
        if (!$inSubmit && $inRoleHas) {
          $temPermission['action'] = 'delete';
        }
        array_push($needUpdateRoles, $temPermission);
      }
    }
    return $needUpdateRoles;
  }

  /**
   * 取得所有权限
   * @param Request $request
   * @return mixed
   */
  private function getAllPermissions(Request $request)
  {
    return Permission::filter($this->getParams($request), PermissionFilter::class)->get();
  }

  /**
   * 递归获取权限树
   * @param $permissions
   * @param array $root
   * @param array $parent
   */
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
