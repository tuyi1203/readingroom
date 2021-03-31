<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FormRequest extends BaseFormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * 修改API返回的错误格式
   * @param Validator $validator
   */
  protected function failedValidation(Validator $validator)
  {
    throw (new HttpResponseException(response()->json([
      'code' => 422,
//      'msg' => $validator->errors(),
      'msg' => $validator->errors()->first(), // 取第一个错误信息
      'data' => null,
      'result' => false,
    ], 200)));
  }

}
