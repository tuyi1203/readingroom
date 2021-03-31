<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Profile\ResetPasswordFormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

use App\Services\ProfileService;

class ProfileController extends APIBaseController
{
  /**
   * 重置密码接口
   * @param ResetPasswordFormRequest $request
   * @return JsonResponse
   */
  public function setPassword(ResetPasswordFormRequest $request)
  {
    //权限检查(默认所有登陆用户都有修改密码的权限)
//    $this->checkPermission('set_password');

    if (!((new ProfileService())->checkPassword($request->input('current_password'), $this->user->password))) {
      return $this->failed('当前密码输入不正确');
    }

    try {
      (new ProfileService())->updateUserPassword($this->user->id, $request->input('new_password'));
    } catch (ModelNotFoundException $exception) {
      return $this->failed('未找到该用户');
    } catch (QueryException $exception) {
      return $this->failed('保存密码失败');
    }

    return $this->success(null, '修改密码成功.');
  }

}
