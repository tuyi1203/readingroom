<?php

namespace App\Http\Requests\Api\Profile;

use App\Http\Requests\FormRequest;

class ResetPasswordFormRequest extends FormRequest
{

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    switch ($this->method()) {
      case 'PUT':
        return [
          'current_password' => [
            'required',
            'string'
          ],
          'new_password' => [
            'required',
            'string'
          ],
          'confirm_password' => [
            'required',
            'string',
            'same:new_password'
          ],
        ];
        break;
      /*    case 'PUT':
              return [
                'dict_name' => 'required|string',
                'remark' => 'nullable|string',
                'order_sort' => 'nullable|numeric'
              ];
              break;*/
    }
  }

  public function attributes()
  {
    return [
      'current_password' => '当前登陆密码',
      'new_password' => '新密码',
      'confirm_password' => '确认新密码',
    ];
  }

  public function messages()
  {
    return [
      'current_password.required' => '请填写当前密码',
      'new_password.required' => '新密码不能为空',
      'confirm_password.required' => '确认密码不能为空',
      'confirm_password.same' => '新密码和确认密码必须相同',
    ];
  }
}
