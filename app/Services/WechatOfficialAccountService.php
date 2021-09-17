<?php

namespace App\Services;

use App\Models\Backend\TeacherNotificationsView;
use Illuminate\Support\Facades\DB;

class WechatOfficialAccountService
{
  public static function sendNotification(TeacherNotificationsView $notification) {
    echo 'in'."\n";
    $weChatApp = app('wechat.official_account');

    // TODO csk83 正式使用需要根据服务号来通知
    $weChatApp->template_message->send([
      'touser' => $notification->open_id,
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
    DB::table('teacher_notification_plans')->where('id', $notification->id)->update(['state' => 4]);
    echo 'out'."\n";
  }
}
