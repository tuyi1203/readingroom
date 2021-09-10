<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherNotificationAttendClassController extends APIBaseController
{
  const NOTIFICATION_TYPE = 'attend_class';

  /***
   * 开关上课通知
   * @param Request $request
   * @return JsonResponse
   */
  public function setting(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'state' => 'required',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $obj = TeacherNotificationSetting::updateOrCreate([
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
    ], [
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
      'state' => $request->input('state'),
    ]);

    if (!$obj) {
      return $this->failed('Update failed.');
    }

    return $this->success([$obj]);
  }

  /***
   * 导入上课通知数据
   * @param Request $request
   * @return JsonResponse
   */
  public function excel(Request $request): JsonResponse
  {
    return $this->success([]);
  }

  /***
   * 获取当前教师的上课通知列表
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
    return $this->success([]);
  }

  /***
   * 获取当前用户的课后延时服务通知列表
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request): JsonResponse
  {
    return $this->success([]);
  }

  /***
   * 获取当前用户的课后延时服务通知列表
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function show(Request $request, int $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   * 获取当前用户的课后延时服务通知列表
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function update(Request $request, int $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   * 获取当前用户的课后延时服务通知列表
   * @param Request $request
   * @param int|null $id
   * @return JsonResponse
   */
  public function destroy(Request $request, int $id = null): JsonResponse
  {
    return $this->success($id);
  }
}
