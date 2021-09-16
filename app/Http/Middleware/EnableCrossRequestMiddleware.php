<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
    $headers = [
      'Access-Control-Allow-Origin' => $origin,
      'Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN, X-Requested-With',
      'Access-Control-Expose-Headers' => 'Authorization, authenticated',
      'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, OPTIONS, DELETE',
      'Access-Control-Allow-Credentials' => 'true',
    ];

    $response = $next($request);
    $allow_origin = explode(',', config('rmxx.allow_origin'));
    if (in_array($origin, $allow_origin)) {
      if ($request->isMethod('OPTIONS')) {
        return response()->json('{"method":"OPTIONS"}', 200, $headers);
      }

      foreach($headers as $key => $value) {
        $response->headers->set($key, $value);
      }
    }

    return $response;
  }
}
