<?php

namespace App\Jobs;

use App\Models\Backend\TeacherNotificationsView;
use App\Services\WechatOfficialAccountService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TeacherNotificationsView $notification)
    {
      $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WechatOfficialAccountService $wechatProcess)
    {
      echo 'test'."\n";
      $wechatProcess::sendNotification($this->notification);
    }

  /**
   * 任务失败的处理过程
   *
   * @param  Exception  $exception
   * @return void
   */
  public function failed(Exception $exception)
  {
    // 给用户发送任务失败的通知，等等……
  }
}
