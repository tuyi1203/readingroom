<?php

namespace App\Http\Controllers\Backend\V1;

use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Log;

class VerificationCodesController extends APIBaseController
{
  /**
   * 发送阿里云短信验证码
   * @param Request $request
   * @param EasySms $easySms
   * @return \Illuminate\Http\JsonResponse
   * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
   */
  public function store(Request $request, EasySms $easySms)
  {
    $validator = Validator::make($request->all(), [
      'mobile' => 'required|string',
    ]);

    if ($validator->fails()) {
      return $this->error(201, $validator->errors()->first('mobile'));
    }

    $mobile = $request->mobile;
    // 生成4位随机数，左侧补0
    $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

    try {
      $result = $easySms->send($mobile, [
        'template' => config('easysms.gateways.aliyun.templates.register'),
        'data' => [
          'code' => $code
        ],
      ]);
    } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
      $message = $exception->getException('aliyun')->getMessage();
      Log::info('短信发送失败：' . $message);
      return $this->error(500, $message ?: '短信发送失败，请稍后再试');
    }

    $key = 'verificationCode_' . Str::random(15);
    $expiredAt = now()->addMinutes(5);
    // 缓存验证码 5 分钟过期。
    Cache::put($key, ['mobile' => $mobile, 'code' => $code], $expiredAt);

    return $this->success([
      'verifykey' => $key,
      'expired_at' => $expiredAt->toDateTimeString(),
    ]);
  }
}
