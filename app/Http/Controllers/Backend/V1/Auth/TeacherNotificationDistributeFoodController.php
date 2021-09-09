<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherNotificationDistributeFoodController extends APIBaseController
{
  /***
   * 开关打饭通知
   * @param Request $request
   * @return JsonResponse
   */
  public function setting(Request $request): JsonResponse
  {
    return $this->success([]);
  }


  /***
   * 导入打饭通知数据
   * @param Request $request
   * @return JsonResponse
   */
  public function excel(Request $request): JsonResponse
  {
    return $this->success([]);
  }

  /***
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
    return $this->success([]);
  }

  /***
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request): JsonResponse
  {
    return $this->success([]);
  }

  /***
   *
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function show(Request $request, int $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   *
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function update(Request $request, int $id): JsonResponse
  {
    return $this->success($id);
  }

  /***
   *
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function destroy(Request $request, int $id): JsonResponse
  {
    return $this->success($id);
  }
}
