<?php

namespace App\Http\Controllers\Backend\V1\Auth;


use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Rules\Mobile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Backend\ProgressBaseinfo;
use Illuminate\Support\Facades\Validator;
use \Spatie\Permission\Exceptions\UnauthorizedException;

class ProgressBaseInfoController extends APIBaseController
{

  public function show(Request $request, $id)
  {


//    if (intval($baseInfo['user_id']) !== intval($this->user->id)) {// 数据权限验证
//      throw new UnauthorizedException(403);
//    }


  }

  /**
   * 显示职称申报教师基本信息详情接口
   * @param Request $request
   * @return JsonResponse
   */
  public function getBaseInfo(Request $request)
  {
    $this->checkPermission('detail_baseinfo');
    $baseInfo = ProgressBaseinfo::where('user_id', $this->user->id)->first();
    if (is_null($baseInfo)) {
      return $this->success(null);
    }
    return $this->success($baseInfo);
  }

  /**
   * 创建或更新职称申报教师基本信息详情接口
   * @param Request $request
   * @return JsonResponse
   */
  public function edit(Request $request)
  {
    $this->checkPermission('add_or_edit_baseinfo');

    $validator = Validator::make($request->all(), [
//      'name' => 'required|string|unique:users',
      'name' => 'required|string',
      'old_name' => 'required|string',
      'min_zu' => 'required',
      'gender' => 'required',
      'id_card' => 'required|string|max:20',
      'company' => 'required',
      'company_type' => 'required',
      'apply_series' => 'required',
      'apply_course' => 'required',
      'had_position' => 'required',
      'apply_position' => 'required',
      'review_team_name' => 'required',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $baseInfo = ProgressBaseinfo::updateOrCreate([
      'user_id' => $this->user->id,
    ], [
      'name' => $request->input('name'),
      'old_name' => $request->input('old_name'),
      'min_zu' => $request->input('min_zu'),
      'gender' => $request->input('gender'),
      'id_card' => $request->input('id_card'),
      'company' => $request->input('company'),
      'company_type' => $request->input('company_type'),
      'apply_series' => $request->input('apply_series'),
      'apply_course' => $request->input('apply_course'),
      'had_position' => $request->input('had_position'),
      'apply_position' => $request->input('apply_position'),
      'review_team_name' => $request->input('review_team_name'),
      'birthday' => $request->input('birthday'),
      'zai_bian' => $request->input('zai_bian'),
    ]);

    if (!$baseInfo) {
      return $this->failed('Update failed.');
    }
    return $this->success($baseInfo, 'Update succeed.');
  }

  /**
   * 插入或更新申报教师基本信息接口
   * @param Request $request
   * @param $id
   */
  public function update(Request $request, $id)
  {

  }

  public function store(Request $request)
  {

  }


}
