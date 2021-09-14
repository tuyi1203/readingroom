<?php

namespace App\Traits;

use \GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait RmxxSystemApiHelperTrait
{

  public function getToken()
  {
    $requestTime = date('Y-m-d H:i:s');
    return [
      'requestor' => env('RMXX_SYSTEM_API_ID'),
      'requestTime' => $requestTime,
      'digest' => md5($requestTime . env('RMXX_SYSTEM_API_KEY')),
    ];
  }


  /**
   * 发送请求接收结果
   * @param $url
   * @param $method
   * @param array|null $params
   * @return bool
   */
  public function sendRequest($url, $method, ?array $params = [])
  {
    try {
      $params = is_array($params) ? array_merge($this->getToken(),$params) : $this->getToken();
      $url = env('RMXX_SYSTEM_API_URL') . $url;
      $client = new Client();
      $response = $client->request($method, $url,
        [
          'headers' => [
            //'Content-Type' => 'application/json',
            'Accept' => 'application/json',
          //  'Authorization' => 'Bearer ' . $token,
          ],
          'query' => $params
        ]);

      $statusCode = $response->getStatusCode();
      if ($statusCode == 200) {
        $content = $response->getBody()->getContents();
        $data = \json_decode($content, true);
        //return $data;
        if($data === false) {
          Log::error($method . '方法调用失败.' . $content . '. 调用参数：' . \json_encode($params, true));
          return false;
        }
        if (isset($data['success']) && $data['success'] == false) {
          Log::error($method . '方法调用失败.' . ($data['msg']??$content) . '. 调用参数：' . \json_encode($params, true));
          return false;
        }
        if (isset($data['result']['total'])) {
          return $data['result'];
        }
        if (isset($data['result']['data'])) {
          return $data['result']['data'];
        }
        if (isset($data['result'])) {
          return $data['result'];
        }
        return $data;
      }
    } catch (\Exception $e) {
      Log::error($method . '方法调用失败.' . $e->getMessage() . '. 调用参数：' . \json_encode($params, true));
    }
    return false;
  }
}
