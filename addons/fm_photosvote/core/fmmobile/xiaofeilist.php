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
	$voteer = pdo_fetch("SELECT nickname,realname FROM ".tablename($this->table_voteer)." WHERE rid = :rid AND from_user = :from_user" , array(':rid' => $rid, ':from_user' => $from_user));

$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		//$total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . ' WHERE rid = :rid AND from_user = :from_user '.$uni.'', array(':rid' => $rid,':from_user' => $from_user));
		$data = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . ' WHERE rid = :rid AND from_user = :from_user '.$uni.' ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize .',' . $psize, array(':rid' => $rid,':from_user' => $from_user));
		foreach ($data as $key => $value) {
			$data[$key]['num'] = '<div class="mui-pull-left mui-badge mui-badge-warning" style="margin-right: 10px; width: 15%;">'.$value['price'].'</div>';
			if ($value['paytype'] == 2) {
				$data[$key]['type'] = '<div class="mui-pull-left mui-btn mui-btn-success">投票</div>';
			}elseif ($value['paytype'] == 3) {
				$data[$key]['type'] = '<div class="mui-pull-left mui-btn mui-btn-info">报名</div>';
			}else{
				$data[$key]['type'] = '<div class="mui-pull-left mui-btn">其他</div>';
			}
			if (!empty($value['paytime'])) {
				$data[$key]['createtime'] = date('Y-m-d', $value['paytime']).'<br />'.date('H:i:s', $value['paytime']);
			}else{
				$data[$key]['createtime'] = '未支付';
			}
			if ($value['status'] == 1) {
				$data[$key]['status'] = '<div class="mui-pull-left mui-btn mui-btn-success">成功</div>';
			}else{
				$data[$key]['status'] = '<div class="mui-pull-left mui-btn">无效</div>';
			}


		}
		//$pager = pagination($total, $pindex, $psize);

	$unrname = $this->getusernames($user['realname'], $voteer['realname'], '6',$voteer['nickname']);

	$title = $unrname . ' 的消费中心';


}
if ($_GPC['op'] == 'sub') {
	include $this -> template('xiaofeilist_sub');
}else{
	$templatename = $rbasic['templates'];
	if ($templatename != 'default' && $templatename != 'stylebase') {
		require FM_CORE . 'fmmobile/tp.php';
	}
	$toye = $this -> templatec($templatename, $_GPC['do']);
	include $this -> template($toye);
}

