<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class DictRequest extends FormRequest
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
          'dict_category' => 'required|numeric',
          'dict_code' => 'string',
          'dict_name' => 'required|string',
          'remark' => 'string',
          'order_sort' => 'nullable|numeric'
        ];
        break;
    }
  }

  public function attributes()
  {
    return [
      'dict_category' => '数据字典类型',
      'dict_code' => '数据字典显示数字',
      'dict_name' => '数据字典名称',
      'remark' => '备注',
      'order_sort' => '排序'
    ];
  }

  /*public function messages()
  {
    return [
      'name.unique' => '用户名已被占用，请重新填写',
      'name.regex' => '用户名只支持英文、数字、横杆和下划线。',
      'name.between' => '用户名必须介于 3 - 25 个字符之间。',
      'name.required' => '用户名不能为空。',
    ];
  }*/
}
