<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
		$afrom_user = $_GPC['afrom_user'];
		$from_user = $_GPC['from_user'];
		$tfrom_user = $_GPC['tfrom_user'];
		
		$keyword = $_GPC['keyword'];
		
		$zan = $_GPC['zan'];
		
		$Where = "";
		if (!empty($keyword)){
			
			$Where .= " AND content LIKE '%{$keyword}%' OR nickname LIKE '%{$keyword}%'";				
			$Where .= " OR ip LIKE '%{$keyword}%'";	
			$t = pdo_fetchall("SELECT from_user FROM ".tablename($this->table_users)." WHERE rid = :rid and nickname LIKE '%{$keyword}%' ".$uni." ", array(':rid' => $rid));
			foreach ($t as $row) {
				$Where .= " OR tfrom_user LIKE '%{$row['from_user']}%'";
			}
		}
		if (!empty($_GPC['isdel'])) {
			if ($_GPC['isdel'] == -1) {
				$isdel = 0;
			}else{
				$isdel = 1;
			}
			$Where .= "AND is_del =".$isdel;
		}

		$now = time();
		$starttime = empty($_GPC['time']['start']) ?  strtotime(date("Y-m-d H:i", $now - 2592000)) : strtotime($_GPC['time']['start']);
		$endtime = empty($_GPC['time']['end']) ?  strtotime(date("Y-m-d H:i", $now + 86400)) : strtotime($_GPC['time']['end']);
		if (!empty($starttime) && !empty($endtime)) {
			$Where .= " AND createtime >= " . $starttime; 
			$Where .= " AND createtime < " . $endtime; 
		}
		if (!empty($from_user)){
			$Where .= " AND `from_user` = '{$from_user}'";		
		}
		if (!empty($tfrom_user)){
			$Where .= " AND `tfrom_user` = '{$tfrom_user}'";		
		}
		if (!empty($afrom_user)){
			$Where .= " AND `afrom_user` = '{$afrom_user}'";		
		}
		if ($zan == 1){
			$Where .= " AND `zan` = 1";		
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得分享点击详细数据
		$messages = pdo_fetchall('SELECT * FROM '.tablename($this->table_bbsreply).' WHERE rid= :rid '.$Where.$uni.'  order by `createtime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid) );
		
		//查询分享人姓名电话结束
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_bbsreply).' WHERE rid= :rid '.$Where.$uni.'  order by `createtime` desc ', array(':rid' => $rid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('web/message');
