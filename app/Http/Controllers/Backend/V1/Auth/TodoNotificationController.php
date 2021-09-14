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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TodoNotificationController extends APIBaseController
{
  /***
   * 导入通知数据
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

        $tmpTimeArr = [
          'distribute_food' => '12:00:00',
          'after_class_service' => '16:00:00',
        ];
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $row[1])) {
          $data[] = [
            'user_id' => $this->user->id,
            'notification_type' => $row[0],
            'plan_date' => $row[1],
            'plan_time' => $tmpTimeArr[$row[0]],
            'plan_datetime' => $row[1].' '.$tmpTimeArr[$row[0]],
          ];
        } else {
          $error[$lineNum] = [
            $row[1],
            '日期格式错误（' . $row[1] . '）',
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
   * 通知开关
   * @param Request $request
   * @return JsonResponse
   */
  public function setting(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|string',
      'state' => 'required|int|min:0|max:1',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $notificationType = $request->input('type');
    $state = $request->input('state', 0);

    $obj = TeacherNotificationSetting::updateOrCreate([
      'user_id' => $this->user->id,
      'notification_type' => $notificationType,
    ], [
      'user_id' => $this->user->id,
      'notification_type' => $notificationType,
      'state' => $state,
    ]);

    if (!$obj) {
      return $this->failed('Update failed.');
    }

    return $this->success($obj);
  }

  /***
   * 获取通知列表
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
    $_where = ['user_id' => $this->user->id];
    $notificationType = $request->input('type');
    if($notificationType){
      $_where['notification_type'] = $notificationType;
    }
    $month = $request->input('month');
    if($month){
      $_where['month'] = $month;
    }

    $fields = explode(',', $request->input('fields', '*'));
    $users = TeacherNotificationPlan::filter($this->getParams($request, $_where), TeacherNotificationPlanFilter::class)
      ->paginate($this->getPageSize($request), $fields, 'page', $this->getCurrentPage($request));

    return $this->success($users->toArray());
  }

  /***
   * 通知单条新增
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|string',
      'plan_date' => 'required|dateFormat:Y-m-d',
      'plan_time' => 'required|dateFormat:H:i:s',
      'state' => 'required|int|min:0|max:1',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $notificationType = $request->input('type');
    $plan_date = $request->input('plan_date');
    $plan_time = $request->input('plan_time');
    $state = $request->input('state', 0);

    $obj = TeacherNotificationPlan::updateOrCreate([
      'user_id' => $this->user->id,
      'notification_type' => $notificationType,
      'plan_date' => $plan_date,
      'plan_time' => $plan_time,
    ], [
      'user_id' => $this->user->id,
      'notification_type' => $notificationType,
      'plan_date' => $plan_date,
      'plan_time' => $plan_time,
      'plan_datetime' => $plan_date.' '.$plan_time,
      'state' => $state,
    ]);

    if (!$obj) {
      return $this->failed('Create failed.');
    }

    return $this->success($obj);
  }

  /***
   * 通知单条与多条删除
   * @param Request $request
   * @param int|null $id
   * @return JsonResponse
   */
  public function destroy(Request $request, int $id = null): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|string',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $notificationType = $request->input('type');
    $ids = $request->input('ids', []);

    if($ids){
      if(!is_array($ids)){
        return $this->failed('ids is not an array.');
      }

      foreach ($ids as  $id) {
        TeacherNotificationPlan::where([
          ['id', $id],
          ['user_id', $this->user->id],
          ['notification_type', $notificationType]
        ])->delete();
      }
    } elseif ($id) {
      $obj = TeacherNotificationPlan::where([
        ['id', $id],
        ['user_id', $this->user->id],
        ['notification_type', $notificationType]
      ])->firstOrFail()->delete();
      if (!$obj) {
        return $this->failed('Delete Failed.');
      }
    }

    return $this->success(null, 'Delete succeed.');
  }

  /***
   * 通知单条修改
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function update(Request $request, int $id): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|string',
      'plan_date' => 'required|dateFormat:Y-m-d',
      'plan_time' => 'required|dateFormat:H:i:s',
      'state' => 'required|int|min:0|max:1',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $notificationType = $request->input('type');
    $plan_date = $request->input('plan_date');
    $plan_time = $request->input('plan_time');
    $state = $request->input('state', 0);

    $obj = TeacherNotificationPlan::where([
      ['id', $id],
      ['user_id', $this->user->id],
      ['notification_type', $notificationType]
    ])->firstOrFail();
    $obj->plan_date = $plan_date;
    $obj->plan_time = $plan_time;
    $obj->plan_datetime = $plan_date.' '.$plan_time;
    $obj->state = $state;
    $obj->save();

    return $this->success($obj);
  }

  /***
   * 获取通知单条记录
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function show(Request $request, int $id): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|string',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $notificationType = $request->input('type');

    $obj = TeacherNotificationPlan::where([
      ['id', $id],
      ['user_id', $this->user->id],
      ['notification_type', $notificationType]
    ])->firstOrFail();

    return $this->success($obj);
  }
}
