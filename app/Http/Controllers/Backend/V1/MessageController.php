<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class MessageController extends APIBaseController
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
}
