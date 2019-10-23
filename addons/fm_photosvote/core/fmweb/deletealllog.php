<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
if (!empty($rid)) {
	pdo_delete($this -> table_log, array('rid' => $rid));
	$users = pdo_fetchall("SELECT * FROM " . tablename($this -> table_users) . " WHERE rid = :rid  " . $uni . " ", array(':rid' => $rid));
	foreach ($users as $key => $value) {
		pdo_update($this -> table_users, array('photosnum' => 0, 'xnphotosnum' => 0, 'hits' => 0, 'xnhits' => 0, 'yaoqingnum' => 0, 'sharenum' => 0), array('from_user' => $value['from_user'], 'rid' => $rid));
	}
	pdo_update($this -> table_reply_display, array('ljtp_total' => 0, 'xunips' => 0, 'unphotosnum' => 0, 'cyrs_total' => 0, 'xuninum' => 0), array('rid' => $rid));
	message('全部删除成功！', referer(), 'success');
}