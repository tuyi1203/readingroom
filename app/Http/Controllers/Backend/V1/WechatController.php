<?php

namespace App\Http\Controllers\Backend\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\V1\APIBaseController;
use Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WechatController extends APIBaseController
{
  protected $weChatApp;
  protected $openid;

  /**
   * Construct
   *
   * WeChatController constructor.
   */
  public function __construct()
  {
//    $this->middleware('auth:backend');
    $this->weChatApp = app('wechat.official_account');
  }

  /**
   * @param Request $request
   * @return mixed
   */
  public function serve(Request $request)
  {
    Log::info('request arrived.');
    $this->weChatApp->server->push(function ($message) {
      if ($message) {
        $method = str::camel('handle_' . $message['MsgType']);
        if (method_exists($this, $method)) {
          $this->openid = $message['FromUserName'];
          return call_user_func_array([$this, $method], [$message]);
        }
        Log::info('无此处理方法:' . $method);
      }
    });

    return $this->weChatApp->server->serve();
  }

  /**
   * 事件引导处理方法（事件有许多，拆分处理）
   *
   * @param $event
   *
   * @return mixed
   */
  protected function handleEvent($event)
  {
    Log::info('事件参数：', [$event]);

    $method = str::camel('event_' . $event['Event']);
    Log::info('处理方法:' . $method);

    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], [$event]);
    }

    Log::info('无此事件处理方法:' . $method);
  }

  /**
   * 扫描带参二维码事件
   *
   * @param $event
   * @return string|void
   */
  public function eventSCAN($event)
  {
    Log::info('二维码扫码事件');
    if (Cache::has($event['EventKey'].'QRCodeUrl')) {
      return $this->markTheLogin($event);
    }
  }

  /**
   * 标记可登录用户
   *
   * @param $event
   * @return string|void
   */
  public function markTheLogin($event)
  {
    Log::info('缓存扫码登陆用户openid');
    if (empty($event['EventKey'])) {
      Log::info('EventKey为空');
      return;
    }
    $eventKey = $event['EventKey'];

    // 关注事件的场景值会带一个前缀需要去掉
    if ($event['Event'] == 'subscribe') {
      $eventKey = str::after($event['EventKey'], 'qrscene_');
    }
    // 标记前端可登陆
    Cache::put($eventKey . 'wxloginopenid', $this->openid, now()->addMinute(30));
    Log::info('EventKey:' . $eventKey, [$event['EventKey']]);
    return "欢迎登陆";
  }

  /**
   * 生成登陆用二维码
   * @param Request $request
   * @return JsonResponse
   * @throws Exception
   */
  public function loginQRCode(Request $request)
  {
    Log::info('生成登陆二维码请求.');
    // 查询senceid参数，如果没有就重新生成一次
    $senceId = $request->input('senceid', null);
    if (!$senceId) {
      $senceId = Uuid::uuid4()->getHex();//删除中横线
    }

    // 缓存微信带参二维码
    if (!$url = Cache::get($senceId . 'QRCodeUrl')) {// 有效期 1 天的二维码
      // 有效期 1 天的二维码
      $qrCode = $this->weChatApp->qrcode;
      $result = $qrCode->temporary($senceId, 3600 * 24);//临时二维码
      $url = $qrCode->url($result['ticket']);

      Cache::put($senceId . 'QRCodeUrl', $url, now()->addDay());// 二维码缓存一天
    }

    return $this->success([
      'sence_id' => $senceId,
      'url' => $url,
    ]);
  }
}
