<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class AwardsExport implements FromCollection
{
  protected $data;

  /**
   * 接收教师获奖数据
   * AwardsExport constructor.
   * @param $data
   */
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * 数组转集合
   * @return Collection|\Illuminate\Support\Collection
   */
  public function collection()
  {
    return new Collection($this->createData());
  }

  /**
   * 整理数据
   */
  public function createData()
  {
    $excelHeader = [
      '校区',
      '姓名',
      '类别',
      '项目内容/发表题目/得奖内容',
      '颁发单位/刊物名称及期数/指导对象/主办单位',
      '刊物刊号/出版社',
      '奖项级别/发表范围',
      '获奖等级',
      '获奖/发表 时间',
      '备注'
    ];
    $excelBody = [];
    $this->data->map(function ($item) use (&$excelBody) {
      $tmp = [
        $item->campus,
        $item->user_name,
        $item->award_name,
        $item->title,
        $item->organization,
        $item->kan_hao_deng,
        $item->award_level,
        $item->award_position,
        $item->the_date,
        $item->remark,
      ];
      array_push($excelBody, $tmp);
    });
    return Arr::prepend($excelBody, $excelHeader);
  }
}



