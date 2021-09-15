<?php

namespace App\Services;

use App\Models\Backend\TeacherNotificationsView;

class WechatOfficialAccountService
{
  public static function sendNotification(TeacherNotificationsView $notification) {
    echo 'in'."\n";
    $weChatApp = app('wechat.official_account');
    $weChatApp->template_message->send([
      'touser' => 'o2zBzwW1jBrvHzoh9VJA4S7cpbSY',//$notification->open_id,
      'template_id' => config('rmxx.tpl_msg_id_'. strtolower($notification->notification_type)),
      //'url' => 'https://easywechat.org',
      /*'miniprogram' => [
        'appid' => 'xxxxxxx',
        'pagepath' => 'pages/xxx',
      ],*/
      'data' => [
        'first' =>  $notification->guid,
        'class' => '测试',
        'time' => '2021-09-14',
        'add' => '鲁能跋扈小学',
        'remark' => '测试'.date('Y-m-d H:i:s'),
      ],
    ]);
    echo 'out'."\n";
  }
}
