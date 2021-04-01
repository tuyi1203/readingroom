<?php


namespace App\Services;


use App\ModelFilters\Backend\ProgressDictCategoryFilter;
use App\Models\Backend\ProgressDictCategory;


class ProgressDictCategoryService extends BaseService
{
  /**
   * 根据搜索条件查找数据字典的分类
   * @param array $inputData
   * @return mixed
   */
  public function getDictCategoryList(Array $inputData)
  {
    return ProgressDictCategory::filter($inputData, ProgressDictCategoryFilter::class)
      ->paginate(
        $this->getPageSize($inputData),
        '*',
        'page',
        $this->getCurrentPage($inputData)
      );

  }

}
