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
  'prefix' => 'v1',
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
      'namespace' => 'V1',
      'middleware' => 'throttle:60,1'
    ], function () {

      Route::prefix('wechat')->group(function(){
        Route::get('getloginqrcode','WechatController@loginQRCode');// 获取微信登陆用二维码
        Route::any('/', 'WechatController@serve');// 本地服务器与微信服务器通信接口
      });

      /* --BEGIN-- 新世纪用接口 */
      Route::prefix('xinshiji')->group(function(){
        Route::get('getloginqrcode','WechatXSJController@loginQRCode');// 获取微信登陆用二维码
        Route::get('openid','WechatXSJController@getOpenId');// 获取微信登陆用二维码
        Route::any('/', 'WechatXSJController@serve');// 本地服务器与微信服务器通信接口
        Route::post('sendtpmsg', 'WechatXSJController@sendTmplateMsg'); //发送模板消息
        Route::post('sendmutitpmsg', 'WechatXSJController@sendMultiTmplateMsg'); //群发模板消息
      });
      /* --END-- 新世纪用接口 */

      Route::post('login', 'AuthController@login');// 首页登陆
      Route::Post('qrlogin','AuthController@QRLogin');// 微信扫码登陆
      Route::Post('bindlogin','AuthController@bindLogin');// 绑定手机号登陆
      Route::post('signup', 'AuthController@signup'); // 注册（暂时不需要）
      Route::post('getverifycode', 'VerificationCodesController@store');// 发送短信验证码接口

      Route::group([
        'middleware' => ['auth:api','throttle:120,1']
      ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::post('sendtpmsg', 'MessageController@sendTmplateMsg');
        Route::post('sendmutitpmsg', 'MessageController@sendMultiTmplateMsg');
      });
    });
  });
