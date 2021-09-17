<?php

namespace App\Console;

use DateTime;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

  /**
   * Define the application's command schedule.
   *
   * @param \Illuminate\Console\Scheduling\Schedule $schedule
   * @return void
   * @throws \Exception
   */
    protected function schedule(Schedule $schedule)
    {
      // 1.读取数据库需要发送的通知丢进队列
      $schedule->command('check:notification')->everyMinute()->withoutOverlapping()->runInBackground();

      // 2.按天每堂课前15分钟 查询有需要上课通知的信息写入数据库
      $timeTable = [];
      $classTimeMap = config('rmxx.class_time_map');
      if (!empty($classTimeMap)) {
        foreach ($classTimeMap as $orderOfDay=>$item) {
          for ($i=1;$i<=7;$i++) {
            $item[$i+1] = empty($item[$i+1]) ? $item[1] : $item[$i+1];
            $dt = new DateTime(date('Y-m-d H:i:s', strtotime($item[$i+1])));
            $dt->modify('-15 minutes');
            $timeTable[$orderOfDay][$i] = $dt->format('G:i');
          }
        }
        foreach ($timeTable as $orderOfDay => $items) {
          foreach ($items as $weekday => $time) {
            $schedule->command('gen:attend-class-notification '.$orderOfDay)->weeklyOn($weekday, $time)->runInBackground();
          }
        }
      }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
