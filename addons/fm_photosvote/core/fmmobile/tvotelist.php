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
	$user = $mygift = pdo_fetch("SELECT * FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));
	$voteer = pdo_fetch("SELECT realname,nickname FROM " . tablename($this -> table_voteer) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));


	$votes = $this->gettvotes($rid, $from_user, $rvote['indexpx']);

	$unrname = $this->getusernames($user['realname'], $voteer['realname'], '6',$voteer['nickname']);

	$title = $unrname . ' 的已投票列表';

	$_share['link'] = $_W['siteroot'] . 'app/' . $this -> createMobileUrl('shareuserview', array('rid' => $rid, 'duli' => '1', 'fromuser' => $from_user, 'tfrom_user' => $from_user));
	//分享URL
	$_share['title'] = $title;
	$_share['content'] = $title;
	$_share['imgUrl'] = $this -> getphotos($fmimage['photos'], $user['avatar'], $rbasic['picture']);
}
if ($_GPC['op'] == 'sub') {
	include $this -> template('tvotelist_sub');
}else{
	$templatename = $rbasic['templates'];
	if ($templatename != 'default' && $templatename != 'stylebase') {
		require FM_CORE . 'fmmobile/tp.php';
	}
	$toye = $this -> templatec($templatename, $_GPC['do']);
	include $this -> template($toye);
}

