<?php
namespace App\Traits;


trait RmxxSystemCommonApiTrait
{

  use RmxxSystemApiHelperTrait;

  /**
   * 查询教师个人信息接口
   */
  public function getUserList(array $data)
  {
    return $this->sendRequest(
      '/sys/sys/Account!findUserByPage.action',
      "GET",
      $data
    );
  }

  /**
   * 查询课表信息接口
   */
  public function getSchooTimetableList(array $data)
  {
    return $this->sendRequest(
      '/sys/stc/OrdinarySubject!findTimetable.action',
      "GET",
      $data
    );
  }
}
