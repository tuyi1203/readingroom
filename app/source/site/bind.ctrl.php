<?php

/**
 * 微信绑定相关操作
 */
defined('IN_IA') or exit('Access Denied');
// error_reporting(E_ALL);
$do = in_array($do, ['default', 'bind_do']) ? $do : 'default';

require_once dirname(__FILE__) . '/auth.php';

load()->model('site');
load()->model('mc');


if ($do == 'default') {
  if (isset($_GPC['back'])) {
    $backurl = base64_decode($_GPC['back']);
  }
  $title = '请完善您的教师信息';
  template('auth/bind');
}

if ($do == 'bind_do') {
  // 表单验证
  $insertData = [
    'name' => $_GPC['name'],
    'openid' => $_W['openid'],
    'mobile' => $_GPC['mobile'],
    'update_time' => date("Y-m-d H:i:s"),
    'avatar_url' => $_W['fans']['tag']['avatar'],
  ];

  $errmsg = checkBindForm($insertData);
  if (count($errmsg) > 0) {
    echo json_encode(['result' => 'fail', 'msg' => $errmsg]);
    exit;
  }

  // 检查该用户是否可以绑定微信
  $userCount = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jm_users') . " WHERE name= :name and mobile= :mobile", [':name' => $insertData['name'], ':mobile' => $insertData['mobile']]);

  if ($userCount < 1) {
    echo json_encode(['result' => 'fail', 'msg' => '只有人民小学教师可以绑定微信，请联系管理员添加教师信息！']);
    exit;
  }

  //取得用户ID
  $user = pdo_fetch('SELECT * FROM ' . tablename('jm_users') . " WHERE name= :name and mobile= :mobile", [':name' => $insertData['name'], ':mobile' => $insertData['mobile']]);

  $result = pdo_update('jm_users', ['openid' => $insertData['openid'], 'avatar_url' => $insertData['avatar_url'],'update_time'=>$insertData['update_time']], ['id' => $user['id']]);

  // 绑定微信信息并跳转到之前的页面
  if (!empty($result)) {
    $return = ['result' => 'success', 'msg' => '绑定成功....', 'jumpto' => $_GPC['backurl']];
  } else {
    $return = ['result' => 'fail', 'msg' => '服务器忙碌，请稍后再试....'];
  }

  echo json_encode($return);
}

function checkBindForm($input)
{
  require_once dirname(__FILE__) . '/validate.php';
  $msg = [];
  $validater = new validate();

  if (empty($input['name'])) {
    $msg[]  = "请输入教师姓名";
  } else if (!($validater->isChinese($input['name'])) || mb_strlen($input['name']) < 2 || mb_strlen($input['name'] > 5)) {
    $msg[]  = "请输入正确的教师姓名";
  }

  if (empty($input['mobile'])) {
    $msg[]  = "请输入电话号码";
  } if ($validater->isPhone($input['mobile'])) {
    $msg[]  = "请输入正确的电话号码";
  }

  return $msg;
}
