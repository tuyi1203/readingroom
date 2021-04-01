<?php


namespace App\Services;


class BaseService
{
  /**
   * 取得页面大小
   * @param $inputData
   * @return mixed
   */
  protected function getPageSize(Array $inputData)
  {
    if (isset($inputData['page_size']) && is_numeric($inputData['page_size'])) {
      return $inputData['page_size'];
    }
    return 10;
  }

  /**
   * 取得当前页码
   * @param $inputData
   * @return int|mixed
   */
  protected function getCurrentPage(Array $inputData)
  {

    if (isset($inputData['page']) && is_numeric($inputData['page'])) {
      return $inputData['page'];
    }
    return 1;
  }
}
