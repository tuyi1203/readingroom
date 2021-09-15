<?php
namespace App\Traits;


trait StudentCommonAPITrait
{

  use StudentAPIHelperTrait;

  /**
   * 获取学校班级数据
   * @return bool
   */
  public function getCampusClassList()
  {
    return $this->sendRequest(
      config('rmxx.student_app_url') . '/students/speciality/get_campus_classes',
      "GET"
    );
  }

  /**
   * 获取特长类别数据
   * @return bool
   */
  public function getSpecialityTypeList()
  {
    return $this->sendRequest(
      config('rmxx.student_app_url') . '/students/speciality/get_speciality_types',
      "GET"
    );
  }
}
