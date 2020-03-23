<?php

namespace App\Http\Controllers\Backend\V1;

use Closure;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class APIBaseController extends Controller
{

  protected $user;//登陆用户

  public function __construct()
  {
    $this->middleware(function ($request, Closure $next) {
      $this->user = $request->user();
      return $next($request);
    });
  }

  /**
   * 权限检查
   * @param $permission
   */
  protected function checkPermission($permission)
  {
    $this->user->hasPermissionTo($permission);
  }

  //成功返回
  protected function success($data, $msg = "ok")
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
  protected function error($code = 422, $msg = "fail", $data = "")
  {
    $result = [
      "code" => $code,
      "msg" => $msg,
      "results" => ["data" => $data],
    ];
    return response()->json($result, 200);
  }

  /**
   * 表单验证错误
   * @param $msg
   * @param int $code
   * @return JsonResponse
   */
  protected function validateError($msg, $code = 201)
  {
    return $this->error($code, $msg);
  }

  /**
   * 取得页面大小
   * @param Request $request
   * @return mixed
   */
  protected function getPageSize(Request $request)
  {
    if (is_numeric($request->input('page_size', 10))) {
      return $request->input('page_size', 10);
    }
    return 10;
  }

  /**
   * 取得当前页码
   * @param Request $request
   * @return int|mixed
   */
  protected function getCurrentPage(Request $request)
  {
    if (is_numeric($request->input('page', 1))) {
      return $request->input('page', 1);
    }
    return 1;
  }

  /**
   * 取得参数
   * @param Request $request
   * @return array
   */
  protected function getParams(Request $request)
  {
    $params = $request->all();
    $params['guard_name'] = $this->getGuardName($request);
    return $params;
  }

  /**
   * 取得认证守卫名
   * @param Request $request
   * @return mixed
   */
  protected function getGuardName(Request $request)
  {
    return $request->get('guard');
  }

  /*
   * 没有操作权限/认证非法
   */
  protected function unauthorized($msg = 'Unauthorized.')
  {
    return $this->error(401, $msg);
  }

  /**
   * 操作失败
   * @param string $msg 错误信息
   * @return JsonResponse
   */
  protected function failed($msg = 'Operate failed!')
  {
    return $this->error(510, $msg);
  }

  /*
   * 请求拒绝
   */
  protected function forbidden()
  {
    return $this->error(403, '请求拒绝');
  }

  //如果返回的数据中有 null 则那其值修改为空 （安卓和IOS 对null型的数据不友好，会报错）
  private function parseNull(&$data)
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
