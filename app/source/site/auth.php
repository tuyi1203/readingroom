<?php

/*
* 检查是否存在于用户列表中，如果不存在则跳转到微信绑定页面
*/
function checkRmAuth($backUrl)
{
  global $_W;
  // 获得openid
  if (!isset($_W['openid'])) {
    die('请在微信中打开。。。');
  } else {
    $openId = $_W['openid'];

    // TODO 对照用户信息
    $sql = 'select * from' . tablename('jm_users') . 'where openid=:openid';
    $userInfo = pdo_fetch($sql, [':openid' => $openId]);
    // print_r($userInfo);
    if (!$userInfo) {
      // 跳转到绑定页面
      header('location: ' . url('site/bind/default', ['back'=>base64_encode($backUrl)]));
    }
  }
}
