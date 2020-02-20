<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
  //成功返回
  public function success($data, $msg = "ok")
  {
    $this->parseNull($data);
    $result = [
      "code" => 0,
      "msg" => $msg,
      "results" => ["data" => $data],
    ];
    return response()->json($result, 200);
  }


  //失败返回
  public function error($code = "422", $msg = "fail", $data = "")
  {
    $result = [
      "code" => $code,
      "msg" => $msg,
      "results" => ["data" => $data],
    ];
    return response()->json($result, 200);
  }

//如果返回的数据中有 null 则那其值修改为空 （安卓和IOS 对null型的数据不友好，会报错）
private
function parseNull(&$data)
{
  if (is_array($data)) {
    foreach ($data as &$v) {
      $this->parseNull($v);
    }
  } else {
    if (is_null($data)) {
      $data = "";
    }
  }
}
}
