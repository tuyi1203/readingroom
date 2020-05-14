<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use App\Models\Backend\ProgressQualificationEducate as Educate;
use App\Models\Backend\ProgressQualificationEducateExperience as EducateExperience;

class ProgressQualificationEducationController extends APIBaseController
{
  public function detail(Request $request)
  {
    $this->checkPermission('detail_qualification_education');
    $detail = Educate::where('user_id', $this->user->id)->first();
    if (!$detail) {
      return $this->success([]);
    }
    $experience = EducateExperience::where('user_id', $this->user->id)->orderby('order_sort', 'asc')->get();
    $detail->experience = $experience;
    return $this->success($detail->toArray());
  }

  public function edit(Request $request)
  {
    $this->checkPermission('add_or_edit_qualification_education');

    $validator = Validator::make($request->all(), [
      // 'name' => 'required|string|unique:users',
      // 'email' => 'required|string|email',
      // 'password' => 'required|string|max:30',
      // 'mobile' => ['required', 'string', new Mobile],
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $educateInfo = Educate::where('user_id', $this->user->id)->firstOrFail();
    $educateInfo->fill([
      'graduate_school' => $request->input('graduate_school'),
      'graduate_time' => $request->input('graduate_time'),
      'education' => $request->input('education'),
      'education_no' => $request->input('education_no'),
      'degree_no' => $request->input('degree_no'),
      'subject' => $request->input('subject'),
      'school_name' => $request->input('school_name'),
    ])->save();

    //删除原始数据
    $experienceForDel = EducateExperience::where('user_id', $this->user->id);
    $experienceForDel->delete();

    //添加新数据
    if ($request->has('experiences')) {
      foreach ($request->input('experiences') as $experience) {
        EducateExperience::create([
          'user_id' => $this->user->id,
          "start_year" => $experience['start_year'],
          "start_month" => $experience['start_month'],
          "end_year" => $experience['end_year'],
          "end_month" => $experience['end_month'],
          "school_name" => $experience['school_name'],
          "education" => $experience['education'],
          "prove_person" => $experience['prove_person'],
          "order_sort" => $experience['order_sort'],
        ]);
      }
    }

    $educateInfo = Educate::where('user_id', $this->user->id)->firstOrFail();
    $experiences = EducateExperience::where('user_id', $this->user->id)->orderby('order_sort', 'asc')->get();
    $educateInfo->experience = $experiences;

    return $this->success($educateInfo->toArray());
  }
}
