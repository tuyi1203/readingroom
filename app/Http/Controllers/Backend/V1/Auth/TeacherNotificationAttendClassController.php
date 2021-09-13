<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Exports\TeacherNotificationPlanExport;
use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Imports\TeacherNotificationPlanImport;
use App\ModelFilters\Backend\TeacherNotificationPlanFilter;
use App\Models\Backend\FileConf;
use App\Models\Backend\FileInfo;
use App\Models\Backend\TeacherNotificationPlan;
use App\Models\Backend\TeacherNotificationSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
      'state' => 'required|int|min:0|max:1',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $state = $request->input('state', 0);

    $obj = TeacherNotificationSetting::updateOrCreate([
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
    ], [
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
      'state' => $state,
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
    /**---Excel文件上传-----------------------------------------------------------------------------------------------*/
    $validator = Validator::make($request->all(), [
      'bize_type' => 'required|string',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $file = $request->file('file');
    $originalName = $file->getClientOriginalName(); // 文件原名
    $ext = $file->getClientOriginalExtension();     // 扩展名
    $realPath = $file->getRealPath();               //临时文件的绝对路径
    $fileSize = $file->getClientSize();
    $fileMimeType = $file->getClientMimeType();

    if (!in_array($ext, ['xls','xlsx','xlsb','xlsm','xlst'])) {
      return $this->failed('请上传Excel文件');
    }

    if (!$request->hasFile('file')) {
      return $this->failed('请选择上传的文件');
    }

    if (!$file->isValid()) {
      return $this->failed('文件上传失败');
    }

    $fileConf = FileConf::where('bize_type', $request->input('bize_type'))->where('enabled', 1)->first();
    if ($fileConf->file_size_limit < $fileSize) {
      return $this->failed('文件尺寸过大，请重新上传');
    }

    $newFileName = (string)Str::uuid(32)->getHex() . '.' . $ext; // 文件新名称
    // 使用我们新建的uploads本地存储空间（目录）
    $newPath = $fileConf->path . date("Y_m_d_H");
    $bool = Storage::disk('upload')->put($newPath . DIRECTORY_SEPARATOR . $newFileName, file_get_contents($realPath));
    if (!$bool) {
      return $this->failed('文件上传失败');
    }

    $fileInfo = FileInfo::create([
      'bize_type' => $request->input('bize_type'),
      'bize_id' => ($request->has('bize_id') && $request->input('bize_id')) ? $request->input('bize_id') : null,
      'original_name' => $originalName,
      'new_name' => $newFileName,
      'file_type' => $fileMimeType,
      'file_size' => $fileSize,
      'file_path' => $newPath,
      'relative_path' => $fileConf->resource_realm,
      'user_id' => $this->user->id,
      'real_path' => $newPath . '/' . $newFileName,
      'del_flg' => 0,
    ]);


    /**---数据导入-----------------------------------------------------------------------------------------------*/
    $tmpExcel = Excel::toArray(new TeacherNotificationPlanImport, $newPath . DIRECTORY_SEPARATOR . $newFileName);
    $error = [];//导入记录与错误提示信息
    $data = [];//有效数据
    if ($tmpExcel[0]) {
      foreach ($tmpExcel[0] as $lineNum => $row) {
        if ($lineNum == 0) {
          continue;
        }

        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $row[0])) {
          $data[] = [
            'user_id' => $this->user->id,
            'notification_type' => self::NOTIFICATION_TYPE,
            'plan_date' => $row[0],
          ];
        } else {
          $error[$lineNum] = [
            $row[0],
            '日期格式错误（' . $row[0] . '）',
          ];
        }
      }
    }

    if ($data) {
      foreach ($data as $item) {
        TeacherNotificationPlan::updateOrCreate($item, $item);
      }
    }


    /**---异常数据导入-----------------------------------------------------------------------------------------------*/
    //Excel::download(new TeacherNotificationPlanExport($error), '教师通知1.xlsx');

    return $this->success([
      'fileId' => $fileInfo->id,
      'created_at' => $fileInfo->created_at,
      'excel' => $tmpExcel,
      'error_excel' => $error,
    ]);
  }

  /***
   * 获取当前教师的上课通知列表
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
    $fields = explode(',', $request->input('fields', '*'));
    $users = TeacherNotificationPlan::filter($this->getParams($request, ['user_id' => $this->user->id, 'notification_type' => self::NOTIFICATION_TYPE]), TeacherNotificationPlanFilter::class)
      ->paginate($this->getPageSize($request), $fields, 'page', $this->getCurrentPage($request));

    return $this->success($users->toArray());
  }

  /***
   * 上课通知单条与多条删除
   * @param Request $request
   * @param int|null $id
   * @return JsonResponse
   */
  public function destroy(Request $request, int $id = null): JsonResponse
  {
    $ids = $request->input('ids');
    if($ids){
      if(!is_array($ids)){
        return $this->failed('ids is not an array.');
      }

      foreach ($ids as  $id) {
        TeacherNotificationPlan::where('id', $id)->delete();
      }
    } elseif ($id) {
      $obj = TeacherNotificationPlan::where('id', $id)->delete();
      if (!$obj) {
        return $this->failed('Delete Failed.');
      }
    }

    return $this->success(null, 'Delete succeed.');
  }

  /***
   * 上课通知单条修改
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function update(Request $request, int $id): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'plan_date' => 'required|dateFormat:Y-m-d',
      'plan_time' => 'required|dateFormat:H:i:s',
      'state' => 'required|int|min:0|max:1',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $plan_date = $request->input('plan_date');
    $plan_time = $request->input('plan_time');
    $state = $request->input('state', 0);

    $obj = TeacherNotificationPlan::where('id', $id)->first();
    $obj->plan_date = $plan_date;
    $obj->plan_time = $plan_time;
    $obj->plan_datetime = $plan_date.' '.$plan_time;
    $obj->state = $state;
    $obj->save();

    return $this->success([$obj]);
  }


  /***
   * 上课通知单条新增
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'plan_date' => 'required|dateFormat:Y-m-d',
      'plan_time' => 'required|dateFormat:H:i:s',
      'state' => 'required|int|min:0|max:1',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $plan_date = $request->input('plan_date');
    $plan_time = $request->input('plan_time');
    $state = $request->input('state', 0);

    $obj = TeacherNotificationPlan::updateOrCreate([
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
      'plan_date' => $plan_date,
      'plan_time' => $plan_time,
    ], [
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
      'plan_date' => $plan_date,
      'plan_time' => $plan_time,
      'plan_datetime' => $plan_date.' '.$plan_time,
      'state' => $state,
    ]);

    if (!$obj) {
      return $this->failed('Create failed.');
    }

    return $this->success([$obj]);
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
}
