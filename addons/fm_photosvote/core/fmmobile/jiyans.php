<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
	require_once FM_CORE . 'plugin/jiyan/lib/class.geetestlib.php';
	$rvote = pdo_fetch("SELECT codekey,codekeykey FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
	define("CAPTCHA_ID", $rvote['codekey']);
	define("PRIVATE_KEY", $rvote['codekeykey']);
	$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
	//session_start();
	$user_id = $_SESSION['user_id'];
	
	if ($_SESSION['gtserver'] == 1) {
	    $result = $GtSdk->success_validate($_GPC['geetest_challenge'], $_GPC['geetest_validate'], $_GPC['geetest_seccode'], $user_id);
	    if ($result) {
	    	$fmdata = array(
				"success" => 1,
				"msg" => 'yesg',
			);
	       echo json_encode($fmdata);
			exit;	
	    } else{
	    	$fmdata = array(
				"success" => -1,
				"msg" => 'nog',
			);
	       echo json_encode($fmdata);
			exit;	
	    }
	}else{
	    if ($GtSdk->fail_validate($_GPC['geetest_challenge'],$_GPC['geetest_validate'],$_GPC['geetest_seccode'])) {
	        
	    	$fmdata = array(
				"success" => 1,
				"msg" => 'yes',
			);
	       echo json_encode($fmdata);
			exit;	
	    }else{
	        
	    	$fmdata = array(
				"success" => -1,
				"msg" => 'no',
			);
	       echo json_encode($fmdata);
			exit;	
	    }
	}
