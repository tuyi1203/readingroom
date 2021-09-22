<?php

namespace App\Console\Commands;

use App\Jobs\SendNotification;
use App\Models\Backend\TeacherNotificationsView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '准备通知信息';

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
    public function handle()
    {
      //$this->info("Check Notification \n".date('Y-m-d H:i:s'));
      $cursor = TeacherNotificationsView::where([
        ['plan_datetime', '<=', date('Y-m-d H:i:s', strtotime(config('rmxx.notification_time')))],
        ['plan_state', '=', 1],
        ['state', '=', 1],
      ])->orderBy('plan_datetime', 'ASC')->cursor();
      foreach ($cursor as $notification) {
        //$this->info($notification);
        // 标记进入队列
        DB::table('teacher_notification_plans')->where('id', $notification->id)->update(['state' => 2]);
        SendNotification::dispatch($notification);
      }
    }
}
