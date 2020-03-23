<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Backend
{
  /**
   * Handle an incoming request.
   *
   * @param Request $request
   * @param \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $request->attributes->add([
      'guard' => 'backend',
    ]);
    return $next($request);
  }
}
