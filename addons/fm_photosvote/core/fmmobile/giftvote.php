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
	if (!empty($tfrom_user)) {
		$tuser = pdo_fetch("SELECT * FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $tfrom_user, ':rid' => $rid));
		$fmimage = $this->getpicarr($uniacid,$rid, $tfrom_user,1);
	}else{
		$fmdata = array(
			"success" => -1,
			"msg" => '没有找到该参赛者',
			"url" => $this->createMobileUrl($_GPC['vfrom'], array('rid'=> $rid, 'tfrom_user' =>$tfrom_user)),
		);
		echo json_encode($fmdata);
		exit;
	}

	$user = pdo_fetch("SELECT * FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));
	$voteer = pdo_fetch("SELECT nickname,realname,avatar FROM " . tablename($this -> table_voteer) . " WHERE rid = :rid AND from_user = :from_user", array(':rid' => $rid, ':from_user' => $from_user));
	$gift = $this->getgift($rid);
	$mygift = $this->getmygift($rid, $from_user);
	$xhtotal = ceil($gift['total']/6);


	$templatename = $rbasic['templates'];
	if ($templatename != 'default' && $templatename != 'stylebase') {
		require FM_CORE . 'fmmobile/tp.php';
	}
	$toye = $this -> templatec($templatename, $_GPC['do']);
	include $this -> template($toye);
}

