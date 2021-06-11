<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Exports\SpecialityAwardsExport;
use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Services\StudentsSpecialityService as Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentsSpecialityAwardController extends APIBaseController
{

  /**
   * 获取学生特长数据
   * @param Request $request
   * @return JsonResponse
   */
  public function getAwards(Request $request)
  {
    $this->checkPermission('speciality_award_search');
    $result = (new Service())->getAwards($request->all());
    if (!$result) {
      return $this->failed('获取学生获奖数据失败');
    }
    return $this->success($result);
  }


  /**
   * 下载文件
   * @param Request $request
   * @return BinaryFileResponse
   */
  public function download(Request $request)
  {
    $this->checkPermission('speciality_award_search');
    $params = $request->all();
    $params['page_size'] = 999999999;
    $infos = (new Service())->getAwards4Output($request->all());
    if (!$infos) {
      return $this->failed('获取学生获奖数据失败');
    }

//    $teachers = ProgressTeacherAward::filter($this->getParams($request), ProgressTeacherAwardFilter::class)
//      ->paginate(999999999, ['*'], 'page', $this->getCurrentPage($request));

    return Excel::download(new SpecialityAwardsExport($infos), '重庆市人民小学学生特长信息一览表.xlsx');

  }


}
