<?php

namespace App\Console\Commands;

use App\Models\Backend\TeacherNotificationContent;
use App\Models\Backend\TeacherNotificationPlan;
use App\Services\RmxxSystemApiService;
use Illuminate\Console\Command;

class GenAttendClassNotification extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'gen:attend-class-notification {orderOfDay}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = '检查当前第几次课开通了通知的数据，并写入通知库';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle(RmxxSystemApiService $service)
  {
    //$this->info("Gen Attend class Notification. \n".date('Y-m-d H:i:s'));
    $orderOfDay = $this->argument('orderOfDay');
    $notifications = $service->getTeacherCourseByClass($orderOfDay);
    foreach ($notifications as $notification) {
      //$this->info(print_r($notification,true));
      $plan_datetime = strtotime($notification['notificationTime']);
      // TODO 这里是否需要检车重复插入？？？？
      $notificationPlan = new TeacherNotificationPlan();
      $notificationPlan->user_id           = $notification['dUser']['id'];
      $notificationPlan->notification_type = 'attend_class';
      $notificationPlan->plan_date         = date('Y-m-d',$plan_datetime);
      $notificationPlan->plan_time         = date('H:i:s',$plan_datetime);
      $notificationPlan->plan_datetime     = $notification['notificationTime'];
      $notificationPlan->state             = 1;
      $notificationPlan->save();
      $notificationContent = new TeacherNotificationContent();
      $notificationContent->plan_id = $notificationPlan->id;
      $notificationContent->content = json_encode([
        'dayOfWeek'  => $notification['dayOfWeek'],
        'orderOfDay' => $notification['orderOfDay'],
        'classNo'    => $notification['classNo'],
        'subject'    => $notification['subject'],
        'teacher'    => $notification['teacher'],
      ]);
      $notificationContent->save();
    }
  }
}
