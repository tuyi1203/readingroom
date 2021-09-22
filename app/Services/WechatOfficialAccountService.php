<?php

namespace App\Services;

use App\Models\Backend\TeacherNotificationsView;
use Illuminate\Support\Facades\DB;



class WechatOfficialAccountService
{
  public static function sendNotification(TeacherNotificationsView $notification) {
    echo 'in'."\n";

    $weChatApp = app('wechat.official_account');

    $msg = [
      'touser' => $notification->open_id,
      'template_id' => config('rmxx.tpl_msg_id_'. strtolower($notification->notification_type)),
    ];

    if ($notification->notification_type == 'attend_class') {
      $content = DB::table('teacher_notification_content')->where('plan_id', $notification->id)->value('content');
      $data = json_decode($content,true);
      if (empty($data)) {
        $msg = array_merge($msg, [
          'data' => [
            'first' => '上课提醒.出错',
            'keyword1' => '未知',  //课程名称
            'keyword2' => '未知',  //上课时间
            'keyword3' => '未知', //上课地点
            'remark' => $notification->id . $content . date('Y-m-d H:i:s'),
          ],
        ]);
      } else {
        $msg = array_merge($msg, [
          'data' => [
            'first' => '有课程即将开始',
            'keyword1' => $data['subject'],  //课程
            'keyword2' => $data['orderOfDay'] . '[' . $data['dayOfWeek'] . ']',  //时间
            'keyword3' => $data['classNo'], //地点
            'remark' => $data['teacher'] . '老师请提前安排 ' . date('Y-m-d H:i:s'),
          ],
        ]);
      }
    } else if ($notification->notification_type == 'after_class_serivce') {
      $msg = array_merge($msg, [
        'data' => [
          'first' =>  '课后延时即将开始',
          'keyword1' => '课后延时服务',
          'keyword2' => $notification->plan_time,
          'keyword3' => '教室',
          'remark' => date('Y-m-d H:i:s'),
        ],
      ]);
    } else if ($notification->notification_type == 'distribute_food') {
      $msg = array_merge($msg, [
        'data' => [
          'first' =>  '学生午饭即将开始',
          'keyword1' => '打饭',
          'keyword2' => $notification->plan_time,
          'keyword3' => '教室',
          'remark' => date('Y-m-d H:i:s'),
        ],
      ]);
    }

    $weChatApp->template_message->send($msg);
    DB::table('teacher_notification_plans')->where('id', $notification->id)->update(['state' => 4]);
    echo 'out'."\n";
  }
}
