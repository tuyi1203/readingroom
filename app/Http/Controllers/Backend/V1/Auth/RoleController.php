<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Facades\Validator;
use App\ModelFilters\Backend\RoleFilter;
use App\Models\Backend\ExtendRole as Role;



class RoleController extends APIBaseController
{
  /**
   * 获取用户角色列表接口
   * 对应roles
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    //权限验证
    $this->checkPermission('list_roles');

    //查询
    $roles = Role::filter($this->getParams($request), RoleFilter::class)
      ->paginate($this->getPageSize($request), ['*'], 'page', $this->getCurrentPage($request));

    foreach ($roles as $index => $role) {
      $permissions = $role->getAllPermissions();
      $roles[$index]['permissions'] = $permissions;
    }

    return $this->success($roles->toArray());
  }

  /**
   * 创建新角色接口
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request)
  {
    //权限检查
    $this->checkPermission('add_roles');

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|unique:roles',
      'name_zn' => 'required|string',
      'order_sort' => 'numeric',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $data = [
      'name' => $request->input('name'),
      'name_zn' => $request->input('name_zn'),
      'order_sort' => $request->input('order_sort', 0),
      'guard_name' => $this->getGuardName($request),
    ];
    $role = new Role($data);
    $res = $role->save();
    if (!$res) {
      Log::error('Create role failed', ['context' => $data]);
      return $this->failed('Create role failed');
    }
    return $this->success(null, 'Successfully created role!');
  }

  /**
   * 删除角色接口
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function destroy(Request $request, $id)
  {
    //权限检查
    $this->checkPermission('delete_roles');

    $role = Role::where('id', $id)->firstOrFail();

    //删除记录
    $result = $role->delete();
    if (!$result) {
      $this->failed('Delete Failed.');
    }
    return $this->success(null, 'Delete succeed.');
  }

  /**
   * 获取角色详情接口
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function show(Request $request, $id)
  {
    //权限检查
    $this->checkPermission('detail_roles');

    $role = Role::where('id', $id)->firstOrFail();
    //刷新权限
    $role->permissions = $role->getAllPermissions();
    return $this->success($role);
  }

  /**
   * 更新角色接口
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function update(Request $request, $id)
  {
    //权限检查
    $this->checkPermission('edit_roles');

    //修改记录
    $role = Role::where('id', $id)->firstOrFail();
    $data = [
      'name' => $request->input('name'),
      'name_zn' => $request->input('name_zn'),
      'order_sort' => $request->input('order_sort', 0),
    ];
    if (!$role->update($data)) {
      return $this->failed('Update failed.');
    }
    return $this->success(null, 'Update succeed.');

  }
}
