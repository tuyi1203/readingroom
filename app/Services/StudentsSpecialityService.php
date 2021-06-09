<?php


namespace App\Services;

use App\Traits\StudentCommonAPITrait;


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


}
