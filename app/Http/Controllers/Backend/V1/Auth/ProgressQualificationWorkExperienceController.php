<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use App\Models\Backend\ProgressQualificationEducate as Educate;
use App\Models\Backend\ProgressQualificationWorkExperience as Experience;
use Illuminate\Support\Facades\Validator;

class ProgressQualificationWorkExperienceController extends APIBaseController
{
  /*
  * 获取基本资格-任教工作经历信息（包括乡村学校任教经历）
  */
  public function detail(Request $request)
  {
    $this->checkPermission('detail_qualification_work_experience');
    // 取得工作年限
    $educateDetail = Educate::where('user_id', $this->user->id)->firstOrFail();
    $experiences = Experience::where('user_id', $this->user->id)->orderby('order_sort', 'asc')->get();
    $detailInfo = [
      'rural_teach_years' => $educateDetail->rural_teach_years,
      'experiences' => $experiences->toArray(),
    ];

    return $this->success($detailInfo);
  }

  /*
  * 修改基本资格-任教工作经历信息（包括乡村学校任教经历）
  */
  public function edit(Request $request)
  {
    $this->checkPermission('add_or_edit_qualification_work_experience');

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
    $educateDetail->rural_teach_years = $request->input('rural_teach_years');
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
          "company" => $experience['company'],
          "affairs" => $experience['affairs'],
          "prove_person" => $experience['prove_person'],
          "order_sort" => $experience['order_sort'],
        ]);
      }
    }

    return $this->detail($request);
  }
}
