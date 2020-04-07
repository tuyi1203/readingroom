<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Models\Backend\ExtendRole as Role;
use App\Models\Backend\User;
use App\ModelFilters\Backend\UserFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\Rules\Mobile;

class UserController extends APIBaseController
{
  /**
   * 获取用户列表接口
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $this->checkPermission('list_users');
    $users = User::filter($this->getParams($request), UserFilter::class)
      ->paginate($this->getPageSize($request), ['*'], 'page',
        $this->getCurrentPage($request));

    if (count($users)) {
      foreach ($users as $index => $user) {
        $user->userInfo;
        $user->getRoleNames();
      }
    }

    return $this->success($users->toArray());
  }

  /**
   * 获取单个用户信息接口
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function show(Request $request, $id)
  {
    $this->checkPermission('detail_users');
    $user = User::where('id', $id)->firstOrFail();
    $user->userInfo;
    $user->getRoleNames();

    return $this->success($user);
  }

  /**
   * 新增用户接口
   * @param Request $request
   * @return JsonResponse
   * @throws \Exception
   */
  public function store(Request $request)
  {
    $this->checkPermission('add_users');

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|unique:users',
      'email' => 'required|string|email',
      'password' => 'required|string|max:30',
      'mobile' => ['required', 'string', new Mobile],
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $user = User::create([
      'name' => $request->input('name'),
      'password' => bcrypt($request->input('password')),
      'email' => $request->input('email'),
      'mobile' => $request->input('mobile'),
    ]);

    $user->userInfo()->create([
      'guid' => Uuid::uuid4()->getHex(),//删除中横线
      'full_name' => $request->input('name'),
      'mobile' => $request->input('mobile'),
    ]);

    // 给用户添加角色
    $user->assignRole($request->input('roles'));

    return $this->success($user);
  }

  /**
   * 修改用户数据
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function update(Request $request, $id)
  {
    $this->checkPermission('add_users');

    $validator = Validator::make($request->all(), [
      'name' => 'required|string',
      'email' => 'required|string|email',
      'password' => 'string|max:30',
      'mobile' => ['required', 'string', new Mobile],
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $user = User::where('id', $id)->firstOrFail();
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    if ($request->filled('password')) {
      $user->password = bcrypt($request->input('password'));
    }
    $user->mobile = $request->input('mobile');
    $user->save();

    //替换成新的角色
    $user->syncRoles($request->input('roles'));

    //更新userinfo数据
    $userInfo = $user->userInfo();
    $userInfo->full_name = $request->input('name');
    $userInfo->mobile = $request->input('mobile');

    return $this->success($user);

  }

  /**
   * 删除用户
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function destroy(Request $request, $id)
  {
    //权限检查
    $this->checkPermission('delete_users');

    $user = User::where('id', $id)->firstOrFail();

    //删除记录
    $result = $user->delete();
    if (!$result) {
      $this->failed('Delete Failed.');
    }
    return $this->success(null, 'Delete succeed.');
  }
}
