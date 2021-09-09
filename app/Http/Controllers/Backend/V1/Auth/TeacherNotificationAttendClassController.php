<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherNotificationAttendClassController extends APIBaseController
{
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
   * @return JsonResponse
   */
  public function show(Request $request, $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   * 获取当前用户的课后延时服务通知列表
   * @param Request $request
   * @return JsonResponse
   */
  public function update(Request $request, $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   * 获取当前用户的课后延时服务通知列表
   * @param Request $request
   * @return JsonResponse
   */
  public function destory(Request $request, $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   * 删除
   * @param Request $request
   * @return JsonResponse
   */
  public function delete(Request $request): JsonResponse
  {
    return $this->success([]);
  }
}
