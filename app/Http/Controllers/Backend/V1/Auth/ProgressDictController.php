<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressDictFilter;
use App\Models\Backend\ProgressDict;
use App\Models\Backend\ProgressDictCategory;
use App\Models\Backend\ProgressBaseinfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DictRequest;
use Illuminate\Support\Arr;

class ProgressDictController extends APIBaseController
{
  /**
   * 获取数据字典列表
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $dictList = [];
    if ($request->has('category_name')) {
      $categoryNames = $request->input('category_name');
      if (!is_array($request->input('category_name'))) {
        $categoryNames = [$request->input('category_name')];
      }
      $categoriesCollection = ProgressDictCategory::whereIn('category_name', $categoryNames)->get();
      $categoryIds =
        $categoriesCollection->map(function ($record) {
          return $record->id;
        });
      $categories = $categoriesCollection->toArray();
      $dictList = ProgressDict::whereIn('dict_category', $categoryIds)
        ->orderby('dict_category', 'asc')
        ->orderby('order_sort', 'asc')
        ->get()
        ->groupBy(function ($dictItem, $dictKey) use ($categories) {
          return Arr::first($categories, function ($cateItem, $cateKey) use ($dictItem, $dictKey) {
            return $dictItem['dict_category'] === $cateItem['id'];
          })['category_name'];
        })
        ->toArray();
    } else {
      $categories = ProgressDictCategory::all()->toArray();
      $dictList = ProgressDict::orderby('dict_category', 'asc')
        ->orderby('order_sort', 'asc')
        ->get()
        ->groupBy(function ($dictItem, $dictKey) use ($categories) {
          return Arr::first($categories, function ($cateItem, $cateKey) use ($dictItem, $dictKey) {
            return $dictItem['dict_category'] === $cateItem['id'];
          })['category_name'];
        })
        ->toArray();
    }

    return $this->success($dictList);

  }

  /**
   * 获取数据字典列表接口
   * @param Request $request
   * @return JsonResponse
   */
  public function search(Request $request)
  {
    $this->checkPermission('course_manage');
    $dictList = ProgressDict::filter($this->getParams($request), ProgressDictFilter::class)
      ->paginate($this->getPageSize($request), ['*'], 'page',
        $this->getCurrentPage($request));

    return $this->success($dictList->toArray());
  }

  /**
   * 添加数据字典列表
   * @param DictRequest $request
   * @return JsonResponse
   */
  public function store(DictRequest $request)
  {
    // 获取数据字典的值
    $this->checkPermission('course_manage');
    $dictValue = ProgressDict::where('dict_category', $request->dict_category)->max('dict_value');
    $dictValue = $dictValue ? ++$dictValue : 1;

    if (!$this->chkSameDict($request->dict_category, $request->dict_name)) {
      return $this->failed('数据字典重复，请修改后重新提交');
    }

    $dict = ProgressDict::create([
      'dict_code' => $request->dict_code,
      'dict_name' => $request->dict_name,
      'dict_value' => $dictValue,
      'dict_category' => $request->dict_category,
      'order_sort' => $request->order_sort ? $request->order_sort : 0,
      'remark' => $request->remark,
    ]);
    return $this->success($dict);
  }

  /**
   * 更新数据字典
   * @param DictRequest $request
   * @param $id
   * @return JsonResponse
   */
  public function update(DictRequest $request, $id)
  {
    $this->checkPermission('course_manage');
    $dict = ProgressDict::find($id);
    $dict->fill([
      'dict_code' => $request->dict_code,
      'dict_name' => $request->dict_name,
      'order_sort' => $request->order_sort ? $request->order_sort : 0,
      'remark' => $request->remark,
    ])->save();
    return $this->success($dict);
  }

  /**
   * 删除数据字典
   * @param $id
   * @return JsonResponse
   */
  public function destroy($id)
  {
    $this->checkPermission('course_manage');
    $dict = ProgressDict::find($id);
    if (!$this->chkForienKey($dict)) {
      return $this->failed('该数据字典被使用中，请删除数据后再删除该字典数据');
    }

    $dict->delete();
    $this->success(null, '成功删除数据.');
  }

  /**
   * 检查外键
   * @param $dict
   * @return bool
   */
  private function chkForienKey($dict)
  {
    switch ($dict->category->category_name) {
      case 'course':
        $baseinfoCnt = ProgressBaseinfo::where('apply_course', $dict->dict_value)->count();
        if ($baseinfoCnt) {
          return false;
        }
        break;
    }
    return true;
  }

  /**
   * 检查是否有相同的数据字典
   * @param $dictCategory
   * @param $dictName
   * @return bool
   */
  private function chkSameDict($dictCategory, $dictName)
  {
    $count = ProgressDict::where('dict_category', $dictCategory)
      ->where('dict_name', $dictName)
      ->count();

    if ($count) {
      return false;
    }
    return true;
  }
}
