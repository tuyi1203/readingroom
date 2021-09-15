<?php

namespace App\Traits;

use App\Models\Backend\ThirdPartyToken;
use \GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait StudentAPIHelperTrait
{

  public function getToken()
  {
    $token = ThirdPartyToken::where('third_party_name', config('rmxx.student_app_name'))
      ->where('token_type', 'Bearer')
      ->orderBy('expire_at', 'desc')
      ->limit(1)
      ->get();
    if ($token->isEmpty() || $this->tokenNeedToBeRefresh($token->first()->expire_at)) {
      $token = $this->getAndSaveNewToken()->access_token;
    } else {
      $token = $token->first()->access_token;
    }
    return $token;
  }

  /**
   * 发送请求接收结果
   * @param $url
   * @param $method
   * @param null $params
   * @return bool
   */
  public function sendRequest($url, $method, $params = null)
  {
    try {
      $token = $this->getToken();
      $client = new Client();
      $response = $client->request($method, $url,
        [
          'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
          ],// 设置请求头为json
          'json' => $params
        ]);

      $statusCode = $response->getStatusCode();
      if ($statusCode == 200) {
        $content = $response->getBody()->getContents();
        $data = \json_decode($content, true);
        if (isset($data['results']['total'])) {
          return $data['results'];
        }
        return $data['results']['data'];
      }
    } catch (\Exception $e) {
      Log::error('GET方法调用失败.' . $e->getMessage() . '. 调用参数：' . \json_encode($params, true));
    }
    return false;
  }

  /**
   * 判断token是否需要刷新(token过期时间小于七天则需要刷新)
   * @param $expire_time
   * @return bool
   */
  private function tokenNeedToBeRefresh($expire_time): bool
  {
    $now = time();
    if (strtotime($expire_time) - $now <= 0
      || floor((strtotime($expire_time) - $now) / 3600 / 24) < 7) {
      return true;
    }
    return false;
  }

  /**
   * 获取新token
   * @return bool
   */
  private function getAndSaveNewToken()
  {
    $client = new Client();
    $baseUrl = config('rmxx.student_token_url');

    $response = $client->request('post', $baseUrl,
      [
        'headers' => [
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
        ],//设置请求头为json
        'json' => [
          'grant_type' => config('rmxx.student_grant_type'),
          'client_id' => config('rmxx.student_app_id'),
          'client_secret' => config('rmxx.student_app_secret'),
          'scope' => config('rmxx.student_app_scope'),
        ]]);

    $statusCode = $response->getStatusCode();
    if ($statusCode == 200) {
      $content = $response->getBody()->getContents();
    } else {
      Log::error('获取学生档案系统token出错.');
      return false;
    }

    $content = json_decode($content, true);

    return ThirdPartyToken::create([
      'third_party_name' => config('rmxx.student_app_name'),
      'token_type' => $content['token_type'],
      'access_token' => $content['access_token'],
      'expire_at' => date('Y-m-d H:i:s', time() + $content['expires_in']),
    ]);

  }
}
