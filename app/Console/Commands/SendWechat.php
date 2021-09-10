<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

class SendWechat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:wechat {open_id} {tpl_id} {content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送微信服务号通知信息';

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
      $openId = $this->argument('open_id');
      $tplId = $this->argument('tpl_id');
      $content = $this->argument('content');
      $weChatApp = app('wechat.official_account');
      $weChatApp->template_message->send([
        'touser' => $openId,
        'template_id' => $tplId,
        'url' => 'https://easywechat.org',
        'miniprogram' => [
          'appid' => 'xxxxxxx',
          'pagepath' => 'pages/xxx',
        ],
        'data' => [
          'key1' => $content,
          'key2' => 'VALUE2',
        ],
      ]);
    }
}
