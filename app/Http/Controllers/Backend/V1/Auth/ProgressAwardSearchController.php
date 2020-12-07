<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressTeacherAwardFilter;

use App\Models\Backend\FileInfo;
use App\Models\Backend\ProgressBaseinfo;
use App\Models\Backend\ProgressDict;
use App\Models\Backend\ProgressDictCategory;
use App\Models\Backend\ProgressTeacherAward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Exports\AwardsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProgressAwardSearchController extends APIBaseController
{
  /**
   * 获取用户列表接口
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $this->checkPermission('teacher_award_search');
    $teachers = ProgressTeacherAward::filter($this->getParams($request), ProgressTeacherAwardFilter::class)
      ->paginate($this->getPageSize($request), ['*'], 'page',
        $this->getCurrentPage($request));

    return $this->success($teachers->toArray());
  }

  /**
   * 下载文件
   * @param Request $request
   * @return BinaryFileResponse
   */
  public function download(Request $request)
  {
    $this->checkPermission('teacher_award_search');
    $teachers = ProgressTeacherAward::filter($this->getParams($request), ProgressTeacherAwardFilter::class)
      ->paginate(999999999, ['*'], 'page', $this->getCurrentPage($request));

    return Excel::download(new AwardsExport($teachers), '教师获奖成果一览表.xlsx');



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
