<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Models\Backend\User;
use App\ModelFilters\Backend\UserFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends APIBaseController
{
  /**
   * 获取用户列表接口
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $users = User::filter($this->getParams($request), UserFilter::class)
      ->paginate($this->getPageSize($request), ['*'], 'page',
        $this->getCurrentPage($request));

    if (count($users)) {
      foreach ($users as $index => $user) {
        $user->userInfo;
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
    $user = User::where('id',$id)->firstOrFail();
    $user->userInfo;

    return $this->success($user);
  }
}
