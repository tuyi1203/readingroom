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

Route::group([
  'prefix' => 'v1',
  'domain' => env('APP_DEV_DOMAIN'),
  'namespace' => 'Backend',
  'middleware' => 'backend',
],
  function () {
    Route::group([
      'namespace' => 'V1',
      'middleware' => 'throttle:60,1'
    ], function () {

      Route::prefix('wechat')->group(function () {
        Route::get('getloginqrcode', 'WechatController@loginQRCode');// 获取微信登陆用二维码
        Route::any('/', 'WechatController@serve');// 本地服务器与微信服务器通信接口
      });

      /* --BEGIN-- 新世纪用接口 */
      /*
      Route::prefix('xinshiji')->group(function () {
        Route::get('getloginqrcode', 'WechatXSJController@loginQRCode');// 获取微信登陆用二维码
        Route::get('openid', 'WechatXSJController@getOpenId');// 获取微信登陆用二维码
        Route::any('/', 'WechatXSJController@serve');// 本地服务器与微信服务器通信接口
        Route::post('sendtpmsg', 'WechatXSJController@sendTmplateMsg'); //发送模板消息
        Route::post('sendmutitpmsg', 'WechatXSJController@sendMultiTmplateMsg'); //群发模板消息
      });
      */
      /* --END-- 新世纪用接口 */

      /*
       * 不需要认证的路由 -- Start
       */
      Route::post('login', 'AuthController@login');// 首页登陆接口
      Route::Post('qrlogin', 'AuthController@QRLogin');// 微信扫码登陆接口
      Route::Post('bindlogin', 'AuthController@bindLogin');// 绑定手机号登陆接口
      Route::post('signup', 'AuthController@signup'); // 注册（暂时不需要）接口
      Route::post('getverifycode', 'VerificationCodesController@store');// 发送短信验证码接口
      Route::get('logout', 'AuthController@logout');

      /*
       * 需要认证的路由 -- Start
       */
      Route::group([
        'namespace' => 'Auth',
        'middleware' => ['auth:backend', 'throttle:120,1']//passport验证
      ], function () {
        //角色接口
        Route::apiResource('roles', 'RoleController');

        // 权限接口
        Route::get('permissions/rid/{rid}', 'PermissionController@index'); // 获取角色权限接口
        Route::get('permissions/pid/{pid}', 'PermissionController@index'); // 获取权限接口
        Route::apiResource('permissions', 'PermissionController');
        Route::put('permissions/rid/{rid}', 'PermissionController@updateRolePermissions'); // 更新角色权限接口


      });
    });
  });
