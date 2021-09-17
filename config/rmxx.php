<?php
return [
  'app_dev_domain' => env('APP_DEV_DOMAIN'),
  'allow_origin' => env('ALLOW_ORIGIN'),

  # 学生档案系统？？？
  'student_token_url'  => env('STUDENT_TOKEN_URL'),
  'student_grant_type' => env('STUDENT_GRANT_TYPE'),
  'student_app_name'   => env('STUDENT_APP_NAME'),
  'student_app_url'    => env('STUDENT_APP_URL'),
  'student_app_id'     => env('STUDENT_APP_ID'),
  'student_app_secret' => env('STUDENT_APP_SECRET'),
  'student_app_scope'  => env('STUDENT_APP_SCOPE'),


   # 人民小学系统
  'sys_api_url' => env('RMXX_SYSTEM_API_URL', 'https://www.cqrm.com'),
  'sys_api_id'  => env('RMXX', 'RMXX'),
  'sys_api_key' => env('Rmxx@2021.com', 'Rmxx@2021.com'),

  # 模板通知ID
  'tpl_msg_id_attend_class'        => env('TEMPLATE_MESSAGE_ID_ATTEND_CLASS'),
  'tpl_msg_id_after_class_service' => env('TEMPLATE_MESSAGE_ID_AFTER_CLASS_SERVICE'),
  'tpl_msg_id_distribute_food'     => env('TEMPLATE_MESSAGE_ID_DISTRIBUTE_FOOD'),

  # 消息提前发送
  'notification_time' => '+10 minutes',

  # 课堂时间对照表
  'class_time_map' => [
    //第几节 => ['接口返回的文本第几节','默认上课时间','星期一','星期二','星期三','星期四','星期五','星期六','星期日'],
    1       => ['第一节',            '08:35',    '08:55', '',     '',    '',     '',     '',     ''],
    2       => ['第二节',            '09:30',    '09:50', '',     '',    '',     '',     '',     ''],
    3       => ['第三节',            '10:40',    '',      '',     '',    '',     '',     '',     ''],
    4       => ['第四节',            '11:30',    '',      '',     '',    '',     '',     '',     ''],
    5       => ['第五节',            '14:00',    '',      '',     '',    '',     '',     '',     ''],
    6       => ['第六节',            '14:55',    '',      '',     '',    '',     '',     '',     ''],
  ],

  # 星期对照关系表
  'weekday_map' => [
    1 => '星期一',
    2 => '星期二',
    3 => '星期三',
    4 => '星期四',
    5 => '星期五',
    6 => '星期六',
    7 => '星期日',
  ],
];
