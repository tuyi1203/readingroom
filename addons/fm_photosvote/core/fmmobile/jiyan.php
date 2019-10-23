<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

	require_once FM_CORE . 'plugin/jiyan/lib/class.geetestlib.php';
	define("CAPTCHA_ID", $rvote['codekey']);
	define("PRIVATE_KEY", $rvote['codekeykey']);
	$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
	session_start();
	$user_id = $_GPC['tfrom_user'];
	$status = $GtSdk->pre_process($user_id);
	$_SESSION['gtserver'] = $status;
	$_SESSION['user_id'] = $user_id;
	echo $GtSdk->get_response_str();



