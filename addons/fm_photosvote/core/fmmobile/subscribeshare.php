<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

			$tfrom = $_GPC['tfrom'];
			$vote = $_GPC['vote'];			
			$tid = $_GPC['tid'];
			
			if (!empty($tid)) {
				$tuser = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uid = :uid and rid = :rid", array(':uid' => $tid,':rid' => $rid));
				$tfrom_user = $tuser['from_user'];
			}else {
				$tfrom_user = $_GPC['tfrom_user'];
			}
				
			include $this->template('subscribeshare');
			