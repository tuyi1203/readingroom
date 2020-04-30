<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Backend\Moral;

class ProgressMoralController extends APIBaseController
{
  public function index(Request $request)
  {
  }

  /**
   * 显示师德师风详情
   * @param Request $request
   * @return JsonResponse
   */
  public function detail(Request $request)
  {
    $this->checkPermission('detail_moral');
    $detail = Moral::where('user_id', $this->user->id)->where('category', $request->input('category'))->first()->toArray();
    if (!$detail) {
      return $this->validateError('No record.');
    }

    $moralInfo = [];
    switch ($request->input('category')) {
      case 'summary':
        $moralInfo = [
          'id' => $detail['id'],
          'category' => $detail['category'],
          'user_id' => $detail['user_id'],
          'summary' => $detail['summary'],
        ];
        break;
      case 'kaohe':
        $moralInfo = [
          'id' => $detail['id'],
          'category' => $detail['category'],
          'user_id' => $detail['user_id'],
          'kaohe' => $detail['kaohe'],
          'niandu1' => $detail['niandu1'],
          'niandu1_kaohe' => $detail['niandu1_kaohe'],
          'niandu2' => $detail['niandu2'],
          'niandu2_kaohe' => $detail['niandu2_kaohe'],
          'niandu3' => $detail['niandu3'],
          'niandu3_kaohe' => $detail['niandu3_kaohe'],
          'niandu4' => $detail['niandu4'],
          'niandu4_kaohe' => $detail['niandu4_kaohe'],
          'niandu5' => $detail['niandu5'],
          'niandu5_kaohe' => $detail['niandu5_kaohe'],
        ];
        break;
      case 'warning':
        $moralInfo = [
          'id' => $detail['id'],
          'category' => $detail['category'],
          'user_id' => $detail['user_id'],
          'warning' => $detail['warning'],
        ];
        break;
      case 'punish':
        $moralInfo = [
          'id' => $detail['id'],
          'category' => $detail['category'],
          'user_id' => $detail['user_id'],
          'punish' => $detail['punish'],
        ];
        break;
    }

    return $this->success($moralInfo);
  }

  /**
   * 创建或更新师德师风信息接口
   * @param Request $request
   * @return JsonResponse
   */
  public function edit(Request $request)
  {
    //权限验证
    $this->checkPermission('add_or_edit_moral');
    $category = $request->input('category');
    if (!$category) {
      return $this->validateError('Need category parms.');
    }

    switch ($category) {
      case 'summary':
        $validator = Validator::make($request->all(), [
          'summary' => 'required|string',
        ]);
        break;
      case 'kaohe':
        $validator = Validator::make($request->all(), [
          'kaohe' => 'required|string',
        ]);
        break;
      case 'warning':
        $validator = Validator::make($request->all(), [
          'warning' => 'required|string',
        ]);
        break;
      case 'punish':
        $validator = Validator::make($request->all(), [
          'punish' => 'required|string',
        ]);
        break;
    }

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    if ($category === 'summary') {
      $moralInfo = Moral::updateOrCreate([
        'user_id' => $this->user->id,
        'category' => $category,
      ], [
        'category' => $request->input('category'),
        'summary' => $request->input('summary'),
      ]);
    }

    if ($category === 'warning') {
      $moralInfo = Moral::updateOrCreate([
        'user_id' => $this->user->id,
        'category' => $category,
      ], [
        'category' => $request->input('category'),
        'warning' => $request->input('warning'),
      ]);
    }

    if ($category === 'punish') {
      $moralInfo = Moral::updateOrCreate([
        'user_id' => $this->user->id,
        'category' => $category,
      ], [
        'category' => $request->input('category'),
        'punish' => $request->input('punish'),
      ]);
    }

    if ($category === 'kaohe') {
      $moralInfo = Moral::updateOrCreate([
        'user_id' => $this->user->id,
        'category' => $category,
      ], [
        'category' => $request->input('category'),
        'kaohe' => $request->input('kaohe'),
        'niandu1' => $request->input('niandu1'),
        'niandu1_kaohe' => $request->input('niandu1_kaohe'),
        'niandu2' => $request->input('niandu2'),
        'niandu2_kaohe' => $request->input('niandu2_kaohe'),
        'niandu3' => $request->input('niandu3'),
        'niandu3_kaohe' => $request->input('niandu3_kaohe'),
        'niandu4' => $request->input('niandu4'),
        'niandu4_kaohe' => $request->input('niandu4_kaohe'),
        'niandu5' => $request->input('niandu5'),
        'niandu5_kaohe' => $request->input('niandu5_kaohe'),
      ]);
    }

    if (!$moralInfo) {
      return $this->failed('Update failed.');
    }

    return $this->success($moralInfo);
  }
}
