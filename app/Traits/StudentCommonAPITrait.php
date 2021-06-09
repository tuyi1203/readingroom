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
      env('STUDENT_APP_URL') . '/students/speciality/get_campus_classes',
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
      env('STUDENT_APP_URL') . '/students/speciality/get_speciality_types',
      "GET"
    );
  }
}
