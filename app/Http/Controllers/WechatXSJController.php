<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
Use Log;

class WechatXSJController extends APIBaseController
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
  protected function handleText($message)
  {
    Log::info('收到文字信息：', $message['content']);

    $keyword = $message['content'];
    if ($keyword == 'openid') {
      return $this->openid;
    }


    Log::info('文字信息处理结束');
  }

  /**
   * 发送模板消息
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendTmplateMsg(Request $request)
  {
    $openId = $request->openid;
    $tmpId = $request->tmpid;
    $data = Arr::except($request->all(), ['openid', 'tmpid']);
    $result = $this->weChatApp->template_message->send([
      'touser' => $openId,
      'template_id' => $tmpId,
      'url' => 'https://baidu.com',
      'data' => $data,
    ]);
//    $users = $this->weChatApp->user->list();

    return $this->success($result);
  }

  /**
   * 群发模板消息
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendMultiTmplateMsg(Request $request)
  {
    $result = $this->weChatApp->user->list();
//    $users = $result['data']['openid'];
    $users = [
      'oTrN7wHE9VFeWOtyFLl0Zs3T5KBA',
      'oTrN7wEd7_NV6Bu0HuavseW6Vy8A',
    ];
    foreach ($users as $openId) {
      $tmpId = $request->tmpid;
      $data = Arr::except($request->all(), ['openid', 'tmpid']);
      $sendRes = $this->weChatApp->template_message->send([
        'touser' => $openId,
        'template_id' => $tmpId,
        'url' => 'https://baidu.com',
        'data' => $data,
      ]);
    }
    return $this->success($sendRes);
  }

  /**
   * 取得用户OPENID
   * @param Request $request
   */
  public function getOpenId(Request $request)
  {
    return $this->success([
      'ni'=>'ok',
    ]);
  }
}
