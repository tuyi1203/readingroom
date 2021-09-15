<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class TodoNotificationRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    switch ($this->method()) {
      case 'POST':
      case 'PUT':
        return [
          'type'      => 'required|string',
          'plan_date' => 'required|date_format:Y-m-d',
          'plan_time' => 'required|date_format:H:i:s',
          'state'     => 'required|int|min:0|max:1',
        ];
        break;
      case 'GET':
        return [
          'type' => 'required|string',
        ];
        break;
      case 'DELETE':
        return [
          'type' => 'required|string',
          'ids'  => 'array',
        ];
        break;
    }
  }

  /*public function attributes()
  {
    return [
      'type'      => '通知类型',
      'plan_date' => '通知日期',
      'plan_time' => '通知时间',
      'state'     => '通知状态',
    ];
  }*/

  /*public function messages()
  {
    return [
      'type.required'         => '通知类型不能为空',
      'plan_date.required'    => '日期不能为空',
      'plan_date.date_format' => '日期格式错误，如：2020-01-10',
      'plan_time.required'    => '时间不能为空',
      'plan_time.date_format' => '时间格式错误，如：13:01:59',
      'state.required'        => '状态不能为空',
      'state.integer'         => '状态只能是数字',
      'state.min'             => '状态只能是数字0或1',
      'state.max'             => '状态只能是数字0或1',
      'ids.array'             => '参数ids只能是数组格式',
    ];
  }*/
}
