<?php
/**
 * 女神来了模块定义
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 * (c) Copyright 2016 FantasyMoons. All Rights Reserved.
 */
defined('IN_IA') or exit('Access Denied');
//查询自己是否参与活动
if (!empty($from_user)) {
	$user = $mygift = pdo_fetch("SELECT realname, nickname FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));
	$voteer = pdo_fetch("SELECT nickname,realname FROM " . tablename($this -> table_voteer) . " WHERE rid = :rid AND from_user = :from_user", array(':rid' => $rid, ':from_user' => $from_user));

	$unrname = $this -> getusernames($user['realname'], $voteer['realname'], '6', $voteer['nickname']);

	$title = $unrname . ' 的消费中心';

}
$templatename = $rbasic['templates'];
if ($templatename != 'default' && $templatename != 'stylebase') {
	require FM_CORE . 'fmmobile/tp.php';
}
$toye = $this -> templatec($templatename, $_GPC['do']);
include $this -> template($toye);
