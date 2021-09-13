<?php

namespace App\Console\Commands;

use App\Jobs\SendNotification;
use App\Models\Backend\TeacherNotificationsView;
use Illuminate\Console\Command;

class CheckNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:check';

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
      $this->info("Check Notification \n".date('Y-m-d H:i:s'));
      $cursor = TeacherNotificationsView::where([
        ['state', '=', 1],
        ['plan_date', '=', date('Y-m-d')],
        ['plan_datetime', '>=', date('Y-m-d H:i:s')]
      ])->orderBy('plan_datetime', 'ASC')->cursor();
      foreach ($cursor as $notification) {
        $this->info($notification);
        SendNotification::dispatch($notification);
      }
    }
}
