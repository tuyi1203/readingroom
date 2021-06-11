<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class SpecialityInfosExport implements FromCollection
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
      '班级',
      '姓名',
      '性别',
      '类别',
      '特长名称',
      '特长等级',
    ];
    $excelBody = [];
    foreach ($this->data as $item) {
      $tmp = [
        $item['student_class']['campus_name'],
        $item['student_class']['grade'] . '级' . $item['student_class']['class'] . '班',
        $item['student']['name'],
        $item['student']['gender_name'],
        $item['speciality_type_name'],
        $item['speciality_name'],
        $item['speciality_level_comment'],
      ];
      array_push($excelBody, $tmp);
    }
    return Arr::prepend($excelBody, $excelHeader);
  }
}



