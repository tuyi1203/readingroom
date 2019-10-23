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
	$user = pdo_fetch("SELECT realname, nickname FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));
	$mygift = $this->getmygift($rid,$from_user);
	$rjifen = pdo_fetchcolumn("SELECT is_open_jifen_sync FROM ".tablename($this->table_jifen)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
	if ($rjifen) {
		load()->model('mc');
		$uid = mc_openid2uid($from_user);
		if (empty($uid)) {
			$uid = $_W['fans']['uid'];
		}
		$type = 'credit1';
		$data = pdo_fetchall("SELECT * FROM " . tablename('mc_credits_record') . ' WHERE uid = :uid AND uniacid = :uniacid AND credittype = :credittype ORDER BY id DESC ', array(':uniacid' => $_W['uniacid'], ':uid' => $uid, ':credittype' => $type));
	}else{
		$data = pdo_fetchall("SELECT * FROM " . tablename($this->table_msg) . ' WHERE rid = :rid AND from_user = :from_user AND type = 4 ORDER BY id DESC ', array(':from_user' => $from_user, ':rid' => $rid));
	}
	$voteer = pdo_fetch("SELECT nickname,realname FROM ".tablename($this->table_voteer)." WHERE rid = :rid AND from_user = :from_user" , array(':rid' => $rid, ':from_user' => $from_user));
	foreach ($data as $key => $value) {
		$value['remark'] = ($rjifen == 1) ? $value['remark'] : $value['content'] ;
		$data[$key]['remark'] = $value['remark'];
		if ($rjifen) {
			$data[$key]['num'] = '<div class="mui-pull-left mui-badge mui-badge-primary">'.$value['num'].'</div>';
		}else{
			$num = findNum($value['content']);
			$data[$key]['num'] = '<div class="mui-pull-left mui-badge mui-badge-primary">'.$num.'</div>';
		}

		$data[$key]['createtime'] = date('Y-m-d', $value['createtime']).'<br />'.date('H:i:s', $value['createtime']);
		$add = strstr($value['remark'],'增加');
		$reduce = strstr($value['remark'],'减少');
		if ($add) {
			$data[$key]['status'] = '<div class="mui-pull-left mui-btn mui-btn-success">增加</div>';
		}elseif ($reduce) {
			$data[$key]['status'] = '<div class="mui-pull-left mui-btn mui-btn-warning">减少</div>';
		}else{
			$data[$key]['status'] = '<div class="mui-pull-left mui-btn">其他</div>';
		}
	}

	$unrname = $this->getusernames($user['realname'], $voteer['realname'], '6',$voteer['nickname']);

	$title = $unrname . ' 的积分中心';


}
function findNum($str=''){
    $str=trim($str);
    if(empty($str)){return '';}
    $result='';
    for($i=0;$i<strlen($str);$i++){
        if(is_numeric($str[$i])){
            $result.=$str[$i];
        }
    }
    return $result;
}
if ($_GPC['op'] == 'sub') {
	include $this -> template('jifenlist_sub');
}else{
	$templatename = $rbasic['templates'];
	if ($templatename != 'default' && $templatename != 'stylebase') {
		require FM_CORE . 'fmmobile/tp.php';
	}
	$toye = $this -> templatec($templatename, $_GPC['do']);
	include $this -> template($toye);
}

