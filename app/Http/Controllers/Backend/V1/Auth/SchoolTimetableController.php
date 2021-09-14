<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Services\RmxxSystemApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolTimetableController extends APIBaseController
{

  /***
   * 获取个人课表
   * @param RmxxSystemApiService $service
   * @return JsonResponse
   */
  public function my(RmxxSystemApiService $service) {
    $mobile = $this->user->userInfo->mobile;
    $result = $service->getSchooTimetableList([
      'tel' => $mobile
    ]);
    if ($result===false) {
      return $this->failed('获取数据失败');
    }
    return $this->success($result);
  }

  /***
   * 获取班级课表
   * @param RmxxSystemApiService $service
   * @param string $classCode
   * @return JsonResponse
   */
  public function getByClass(RmxxSystemApiService $service, string $classCode) {
    $result = $service->getSchooTimetableList([
      'class' => $classCode
    ]);
    if ($result===false) {
      return $this->failed('获取数据失败');
    }
    return $this->success($result);
  }

  /***
   * 获取班级课表
   * @param RmxxSystemApiService $service
   * @return JsonResponse
   */
  public function getAll(RmxxSystemApiService $service) {
    $result = $service->getSchooTimetableList([]);
    if ($result===false) {
      return $this->failed('获取数据失败');
    }
    return $this->success($result);
  }

}
