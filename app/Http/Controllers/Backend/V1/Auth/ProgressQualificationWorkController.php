<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use App\Models\Backend\ProgressQualificationWork as Work;
use Illuminate\Support\Facades\Validator;

class ProgressQualificationWorkController extends APIBaseController
{
  public function detail(Request $request)
  {
    $this->checkPermission('detail_qualification_work');
    $detail = Work::where('user_id', $this->user->id)->first();
    if (!$detail) {
      return $this->success([]);
    }
    return $this->success($detail->toArray());
  }

  public function edit(Request $request)
  {
    $this->checkPermission('add_or_edit_qualification_work');

    $validator = Validator::make($request->all(), [
      // 'name' => 'required|string|unique:users',
      // 'email' => 'required|string|email',
      // 'password' => 'required|string|max:30',
      // 'mobile' => ['required', 'string', new Mobile],
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $workInfo = Work::where('user_id', $this->user->id)->firstOrFail();
    $workInfo->fill([
      'work_time' => $request->input('work_time'),
      'teach_years' => $request->input('teach_years'),
      'teach5years' => $request->input('teach5years'),
      'apply_up' => $request->input('apply_up'),
      'apply_course' => $request->input('apply_course'),
      'school_manager' => $request->input('school_manager'),
      'title' => $request->input('title'),
      'qualification_time' => $request->input('qualification_time'),
      'work_first_time' => $request->input('work_first_time'),
      'middle_school_teacher' => $request->input('middle_school_teacher'),
      'middle_school_time' => $request->input('middle_school_time'),
      'remark' => $request->input('remark'),
    ])->save();

    $workInfo = Work::where('user_id', $this->user->id)->firstOrFail();

    return $this->success($workInfo->toArray());
  }
}
