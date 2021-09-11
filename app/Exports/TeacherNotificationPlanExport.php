<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class TeacherNotificationPlanExport implements FromCollection
{
  protected $data;

  /**
   * 接收数据
   * TeacherNotificationPlan constructor.
   * @param $data
   */
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * 数组转集合
   * @return Collection|Collection
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
      '日期',
      '备注',
    ];
    $excelBody = [];
    foreach ($this->data as $lineNum=>$item) {
      $tmp = [
        $item[0],
        $item[1],
      ];
      array_push($excelBody, $tmp);
    }

    return Arr::prepend($excelBody, $excelHeader);
  }
}
