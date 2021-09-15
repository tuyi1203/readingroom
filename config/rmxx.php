<?php
return [
  'app_dev_domain' => env('APP_DEV_DOMAIN'),

   # 人民小学系统
  'sys_api_url' => env('RMXX_SYSTEM_API_URL', 'https://www.cqrm.com'),
  'sys_api_id'  => env('RMXX', 'RMXX'),
  'sys_api_key' => env('Rmxx@2021.com', 'Rmxx@2021.com'),

  # 模板通知ID
  'tpl_msg_id_attend_class'        => env('TEMPLATE_MESSAGE_ID_ATTEND_CLASS'),
  'tpl_msg_id_after_class_service' => env('TEMPLATE_MESSAGE_ID_AFTER_CLASS_SERVICE'),
  'tpl_msg_id_distribute_food'     => env('TEMPLATE_MESSAGE_ID_DISTRIBUTE_FOOD'),

  # 学生档案系统？？？
  'student_token_url'  => env('STUDENT_TOKEN_URL'),
  'student_grant_type' => env('STUDENT_GRANT_TYPE'),
  'student_app_name'   => env('STUDENT_APP_NAME'),
  'student_app_url'    => env('STUDENT_APP_URL'),
  'student_app_id'     => env('STUDENT_APP_ID'),
  'student_app_secret' => env('STUDENT_APP_SECRET'),
  'student_app_scope'  => env('STUDENT_APP_SCOPE'),
];
