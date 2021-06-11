<?php


namespace App\Services;

use App\Models\Backend\ProgressDict;
use App\Models\Backend\ProgressDictCategory;
use App\Traits\StudentCommonAPITrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use function _\first;


class StudentsSpecialityService extends BaseService
{
  use StudentCommonAPITrait;

  /**
   * 获取学校班级数据
   * @return bool
   */
  public function getCampusClasses()
  {
    return $this->getCampusClassList();
  }

  /**
   * 获取学生特长数据
   * @param $params
   * @return bool
   */
  public function getInfos($params)
  {
    return $this->sendRequest(
      env('STUDENT_APP_URL') . '/students/speciality/infos',
      "GET",
      $params
    );
  }

  /**
   * 获取学生特长数据
   * @param $params
   * @return void
   */
  public function getInfos4Output($params)
  {
    $infoData = $this->sendRequest(
      env('STUDENT_APP_URL') . '/students/speciality/infos',
      "GET",
      $params
    )['data'];

    $dicts = $this->getDicts(['gender', 'campus']);

    $types = $this->getSpecialityTypes(null);

    foreach ($infoData as &$item) {
      // 获取校区名称
      $item['student_class']['campus_name'] = Arr::first($dicts['campus'], function ($value , $key) use ($item) {
        return $value['dict_value'] == $item['student_class']['campus'];
      })['dict_name'];

      // 获取性别名称
      $item['student']['gender_name'] = Arr::first($dicts['gender'], function ($value , $key) use ($item) {
        return $value['dict_value'] == $item['student']['gender'];
      })['dict_name'];

      // 获取特长类别
      $item['speciality_type_name'] = Arr::first($types, function ($value , $key) use ($item) {
        return $value['dict_value'] == $item['speciality_type'];
      })['dict_name'];
    }

    return $infoData;

  }

  /**
   * 获取学生特长名称列表
   * @param $params
   * @return bool
   */
  public function getInfoNames($params)
  {
    return $this->sendRequest(
      env('STUDENT_APP_URL') . '/students/speciality/get_info_names',
      "GET",
      $params
    );
  }

  /**
   * 获取学生特长类别数据
   * @param $params
   * @return bool
   */
  public function getSpecialityTypes($params)
  {
    return $this->sendRequest(
      env('STUDENT_APP_URL') . '/students/speciality/get_speciality_types',
      "GET",
      $params
    );
  }

  /**
   * 获取数据字典列表
   * @param array $params
   * @return JsonResponse
   */
  public function getDicts(Array $params)
  {
    $dictList = [];
    if (isset($params['category_name'])) {
      $categoryNames = $params['category_name'];
      if (!is_array($params['category_name'])) {
        $categoryNames = [$params['category_name']];
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

    return $dictList;

  }


}
