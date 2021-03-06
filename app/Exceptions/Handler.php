<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Log;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
  /**
   * A list of the exception types that are not reported.
   *
   * @var array
   */
  protected $dontReport = [
    //
  ];

  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array
   */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  /**
   * Report or log an exception.
   *
   * @param \Exception $exception
   * @return void
   *
   * @throws \Exception
   */
  public function report(Exception $exception)
  {
    parent::report($exception);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Exception $exception
   * @return Response
   *
   * @throws \Exception
   */
  public function render($request, Exception $exception)
  {
    //记录异常
    Log::info($exception->getMessage());

    //权限不存在
    if ($exception instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist) {
      $result = [
        "code" => 403,
        "msg" => 'Permission does not exists.',
      ];
      return response()->json($result, 200);
    }

    //传递的参数有问题
    /*
    if ($exception instanceof \ErrorException) {
      $result = [
        "code" => 500,
        "msg" => 'Error occurs.',
      ];
      return response()->json($result, 200);
    }
    */

    //没有操作权限异常
    if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
      $result = [
        "code" => 403,
        "msg" => 'Unauthorized.',
      ];
      return response()->json($result, 200);
    }

    //找不到数据记录异常

    if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
      $result = [
        "code" => 510,
        "msg" => 'Not found records.',
      ];
      return response()->json($result, 200);
    }

    if ($request->is("api/*")) {
      //如果错误是 ValidationException的一个实例，说明是一个验证的错误
      if ($exception instanceof ValidationException) {
        foreach ($exception->errors() as $key => $val) {
          $data[$key] = $val[0];
        }
        $result = [
          "result" => false,
          "code" => 422,
          //这里使用 $exception->errors() 得到验证的所有错误信息，是一个关联二维数组，所以                使用了array_values()取得了数组中的值，而值也是一个数组，所以用的两个 [0][0]
          "msg" => '信息验证失败',
          "data" => $data,
        ];
        return response()->json($result);
      }
    }


    if ($exception instanceof  \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
      $result = [
        "code" => 404,
        "msg" => 'Not found API.',
      ];
      return response()->json($result, 200);
    }

    return parent::render($request, $exception);
  }
}
