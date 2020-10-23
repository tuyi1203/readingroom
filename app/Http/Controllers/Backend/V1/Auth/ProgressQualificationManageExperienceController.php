<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use App\Models\Backend\ProgressQualificationEducate as Educate;
use App\Models\Backend\ProgressQualificationManageExperience as Experience;
use Illuminate\Support\Facades\Validator;

class ProgressQualificationManageExperienceController extends APIBaseController
{
  /*
  * 获取基本资格-管理工作经历信息（包括乡村学校任教经历）
  */
  public function detail(Request $request)
  {
    $this->checkPermission('detail_qualification_manage_experience');
    // 取得工作年限
    $educateDetail = Educate::where('user_id', $this->user->id)->firstOrFail();
    $experiences = Experience::where('user_id', $this->user->id)->orderby('order_sort', 'asc')->get();
    $detailInfo = [
      'id' => $educateDetail->id,
      'manage_years' => $educateDetail->manage_years,
      'experience' => $experiences->toArray(),
    ];

    return $this->success($detailInfo);
  }

  /*
  * 修改基本资格-管理工作经历信息（包括乡村学校任教经历）
  */
  public function edit(Request $request)
  {
    $this->checkPermission('add_or_edit_qualification_manage_experience');

    $validator = Validator::make($request->all(), [
      // 'name' => 'required|string|unique:users',
      // 'email' => 'required|string|email',
      // 'password' => 'required|string|max:30',
      // 'mobile' => ['required', 'string', new Mobile],
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }
    $educateDetail = Educate::where('user_id', $this->user->id)->firstOrFail();
    $educateDetail->manage_years = $request->input('manage_years');
    $educateDetail->save();

    //删除原始数据
    $experienceForDel = Experience::where('user_id', $this->user->id);
    $experienceForDel->delete();

    //添加新数据
    if ($request->has('experiences')) {
      foreach ($request->input('experiences') as $experience) {
        Experience::create([
          'user_id' => $this->user->id,
          "start_year" => $experience['start_year'],
          "start_month" => $experience['start_month'],
          "end_year" => $experience['end_year'],
          "end_month" => $experience['end_month'],
          "affairs" => $experience['affairs'],
          "prove_person" => $experience['prove_person'],
          "order_sort" => $experience['order_sort'],
        ]);
      }
    }

    return $this->detail($request);
  }
}
