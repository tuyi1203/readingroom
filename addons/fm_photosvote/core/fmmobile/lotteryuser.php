<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

if ($rshare['subscribe'] && !$follow) {
	$fmdata = array(
		"success" => -1,
		"flag" => 5,
		"msg" => '请先关注',
	);
	echo json_encode($fmdata);
	exit();
}
//查询是否存在了
$user = pdo_getcolumn('fm_photosvote_wm_user', array('rid' => $rid,'from_user' => $from_user), 'id',1);
if( $user ){
	$fmdata = array(
		"msg" => '您已经保存过资料了',
	);
}else{
	$user = pdo_getcolumn('fm_photosvote_wm_user', array('rid' => $rid,'tel' => $_GPC['tel']), 'id',1);
	if( $user ){
		$fmdata = array(
			"msg" => '一个手机号只能参与一次！',
		);
	}else{
		//插入资料
		$data['rid'] = $rid;
		$data['from_user'] = $from_user;
		$data['name'] = $_GPC['name'];
		$data['tel'] = $_GPC['tel'];
		$data['time'] = time();
		pdo_insert('fm_photosvote_wm_user', $data);
		$fmdata = array(
			"success" => 1,
			"msg" => '资料保存成功！',
		);
	}
}
echo json_encode($fmdata);exit;