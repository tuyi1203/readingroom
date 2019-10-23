<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

		
		$id = $_GPC['userid'];    
		$rid = $_GPC['rid'];    
		
		$where = '';
		$where .= " AND status = '1'";
		$where .= " AND id = ".$id;


		$userinfo = pdo_fetch('SELECT * FROM '.tablename($this->table_users).' WHERE rid = :rid '.$where.' ', array(':rid' => $rid) );
		$pics = pdo_fetch('SELECT * FROM '.tablename($this->table_users_picarr).' WHERE rid = :rid and from_user = :from_user ', array(':rid' => $rid,':from_user' => $userinfo['from_user']) );
		//$imgs = array();
		//foreach ($pics as $key => $value) {
		//	$imgs .= $value['photos'];
		//}
		$data = array(
				"code" => 103,
				"mesg" => $userinfo,
				"imgs" => $userinfo['avatar']
			);
    	echo json_encode($data);
		exit;