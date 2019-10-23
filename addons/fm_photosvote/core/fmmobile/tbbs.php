<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
	
		$rb = array();
		if ($rvote['tmyushe'] == 1) {
			//预设
			$ybbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE rid = :rid AND status = '9' order by `id` desc ",  array(':rid' => $rid));		
			foreach ($ybbsreply as $r) {
				$rb[] .= $r['nickname'] . ' : ' . cutstr($r['content'], '15');
			}
		}			
		
		//评论
		$bbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE tfrom_user = :tfrom_user AND rid = :rid AND is_del = 0 order by `id` desc ",  array(':tfrom_user' => $tfrom_user,':rid' => $rid));
		if (empty($bbsreply)) {
			//预设
			$ybbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE rid = :rid AND status = '9' order by `id` desc ",  array(':rid' => $rid));		
			foreach ($ybbsreply as $r) {
				$rb[] .= $r['nickname'] . ' : ' . cutstr($r['content'], '15');
			}
		} else {
			foreach ($bbsreply as $r) {
				$rb[] .= $r['nickname'] . ' : ' . cutstr($r['content'], '15');
			}
		}
		
		echo json_encode($rb);
		exit();	