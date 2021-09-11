<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\TeacherNotificationPlanFilter;
use App\Models\Backend\TeacherNotificationPlan;
use App\Models\Backend\TeacherNotificationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherNotificationDistributeFoodController extends APIBaseController
{
  const NOTIFICATION_TYPE = 'distribute_food';

  /***
   * 开关打饭通知
   * @param Request $request
   * @return JsonResponse
   */
  public function setting(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'state' => 'required',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $obj = TeacherNotificationSetting::updateOrCreate([
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
    ], [
      'user_id' => $this->user->id,
      'notification_type' => self::NOTIFICATION_TYPE,
      'state' => $request->input('state'),
    ]);

    if (!$obj) {
      return $this->failed('Update failed.');
    }

    return $this->success([$obj]);
  }


  /***
   * 导入打饭通知数据
   * @param Request $request
   * @return JsonResponse
   */
  public function excel(Request $request): JsonResponse
  {
    $fields = explode(',', $request->input('fields', '*'));
    $users = TeacherNotificationPlan::filter($this->getParams($request, ['user_id' => $this->user->id, 'notification_type' => self::NOTIFICATION_TYPE]), TeacherNotificationPlanFilter::class)
      ->paginate($this->getPageSize($request), $fields, 'page', $this->getCurrentPage($request));

    return $this->success($users->toArray());
  }

  /***
   * 打饭通知查询
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
