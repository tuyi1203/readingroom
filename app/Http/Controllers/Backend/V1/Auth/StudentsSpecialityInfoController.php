<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Services\StudentsSpecialityService as Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentsSpecialityInfoController extends APIBaseController
{
  /**
   * 获取级联选择用学校班级数据
   * @param Request $request
   * @return JsonResponse
   */
  public function getCampusClasses(Request $request)
  {
    $result = (new Service())->getCampusClasses();
    if (!$result) {
      return $this->failed('获取班级数据失败');
    }
    return $this->success($result);
  }

  /**
   * 获取学生特长数据
   * @param Request $request
   * @return JsonResponse
   */
  public function getInfos(Request $request)
  {
    $this->checkPermission('speciality_info_search');
    $result = (new Service())->getInfos($request->all());
    if (!$result) {
      return $this->failed('获取学生特长数据失败');
    }
    return $this->success($result);
  }

  /**
   *  获取学生类别列表
   * @param Request $request
   * @return JsonResponse
   */
  public function getSpecialityTypes(Request $request)
  {
    $this->checkPermission('speciality_info_search');
    $result = (new Service())->getSpecialityTypes($request->all());
    if (!$result) {
      return $this->failed('获取特长类别数据失败');
    }
    return $this->success($result);
  }

  /**
   * 获取学生特长名称列表
   * @param Request $request
   * @return JsonResponse
   */
  public function getInfoNames(Request $request)
  {
    $this->checkPermission('speciality_info_search');
    $result = (new Service())->getInfoNames($request->all());
    if (!$result) {
      return $this->failed('获取特长名称数据失败');
    }
    return $this->success($result);
  }


}
