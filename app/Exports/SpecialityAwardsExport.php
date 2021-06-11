<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class SpecialityAwardsExport implements FromCollection
{
  protected $data;

  /**
   * 接收学生特长数据
   * SpecialityInfosExport constructor.
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
      '校区',
      '班级',
      '姓名',
      '性别',
      '类别',
      '比赛名称',
      '获奖等级/荣誉称号',
      '颁奖机构',
    ];
    $excelBody = [];
    foreach ($this->data as $item) {
//      $organizations = [];
      $tmp = [
        $item['student_class']['campus_name'],
        $item['student_class']['grade'] . '级' . $item['student_class']['class'] . '班',
        $item['student']['name'],
        $item['student']['gender_name'],
        $item['speciality_type_name'],
        $item['title'],
        $item['prize_name']['prize_name'],
        implode(',', data_get($item['organizations'], '*.organization_name')),
      ];
      array_push($excelBody, $tmp);
    }
    return Arr::prepend($excelBody, $excelHeader);
  }
}



