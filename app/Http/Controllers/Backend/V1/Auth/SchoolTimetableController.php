<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Services\RmxxSystemApiService;
use Illuminate\Http\JsonResponse;

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
  public function getByClass(RmxxSystemApiService $service, string $classCode): JsonResponse
  {
    $result = $service->getSchooTimetableList([
      'class' => $classCode
    ]);
    if ($result===false) {
      return $this->failed('获取数据失败');
    }
    return $this->success($result);
  }

  /***
   * 获取所有课表(列表)
   * @param RmxxSystemApiService $service
   * @return JsonResponse
   */
  public function getAllList(RmxxSystemApiService $service): JsonResponse
  {
    $result = $service->getSchooTimetableList([]);
    if ($result===false) {
      return $this->failed('获取数据失败');
    }
    return $this->success($result);
  }

  /***
   * 获取班级课表(树形结构)
   * @param RmxxSystemApiService $service
   * @return JsonResponse
   */
  public function getAllTree(RmxxSystemApiService $service): JsonResponse
  {
    $result = $service->getSchoolTimetableTree();
    return $this->success($result);
  }


  public function tests(RmxxSystemApiService $service)
  {
    dump($service->getTeacherCourseByClass(1));

  }

}
