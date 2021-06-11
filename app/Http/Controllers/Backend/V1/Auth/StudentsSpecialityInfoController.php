<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Exports\SpecialityInfosExport;
use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Services\StudentsSpecialityService as Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentsSpecialityInfoController extends APIBaseController
{

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

  /**
   * 下载文件
   * @param Request $request
   * @return BinaryFileResponse
   */
  public function download(Request $request)
  {
    $this->checkPermission('speciality_info_search');
    $params = $request->all();
    $params['page_size'] = 999999999;
    $infos = (new Service())->getInfos4Output($request->all());
    if (!$infos) {
      return $this->failed('获取学生特长数据失败');
    }

//    $teachers = ProgressTeacherAward::filter($this->getParams($request), ProgressTeacherAwardFilter::class)
//      ->paginate(999999999, ['*'], 'page', $this->getCurrentPage($request));

    return Excel::download(new SpecialityInfosExport($infos), '重庆市人民小学学生特长信息一览表.xlsx');

    /*$headers = [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    return response()
      ->download(
        storage_path('app/secure_upload') . $file->file_path . '/' . $file->new_name,
        $file->original_name,
        $headers
      );*/

  }


}
