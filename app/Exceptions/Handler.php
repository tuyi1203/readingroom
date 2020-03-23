<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;

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
   * @return \Symfony\Component\HttpFoundation\Response
   *
   * @throws \Exception
   */
  public function render($request, Exception $exception)
  {
    //记录异常
    Log::info($exception->getMessage());

    //没有权限异常
    if ($exception instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist) {
      $result = [
        "code" => 401,
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

    return parent::render($request, $exception);
  }
}
