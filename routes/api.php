<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
$devAPPParams = [
  'prefix' => 'v1/auth',
//  'version' => 'v1',
  'domain' => env('APP_DEV_DOMAIN'),
  'namespace' => 'Api',
];

Route::group(
//  [
//  'prefix' => 'auth',
//  'version' => 'v1',
//  'domain' => env('APP_DEV_DOMAIN'),
//],
  $devAPPParams,
  function () {
    Route::group([
      'namespace' => 'V1'
    ], function () {
      Route::post('login', 'AuthController@login');
      Route::post('signup', 'AuthController@signup');

      Route::group([
        'middleware' => 'auth:api'
      ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
      });
    });
  });
