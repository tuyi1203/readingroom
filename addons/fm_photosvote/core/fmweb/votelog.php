<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
$op = ($_GPC['op'] == 'loupiao') ? 'loupiao' : 'display';
if ($op == 'display') {
	$rvote = pdo_fetch("SELECT votepay FROM " . tablename($this -> table_reply_vote) . " WHERE rid = :rid", array(':rid' => $rid));
	$from_user = $_GPC['from_user'];
	$afrom_user = $_GPC['afrom_user'];
	$tfrom_user = $_GPC['tfrom_user'];
	load() -> model('mc');
	$where = "";
	$keyword = $_GPC['keyword'];
	if (!empty($keyword)) {
		$where .= " AND nickname LIKE '%{$keyword}%'";
		$where .= " OR ip LIKE '%{$keyword}%'";
		$where .= " OR from_user LIKE '%{$keyword}%'";
		$t = pdo_fetchall("SELECT from_user FROM " . tablename($this -> table_users) . " WHERE rid = :rid and nickname LIKE '%{$keyword}%' '.$uni.'", array(':rid' => $rid));
		foreach ($t as $row) {
			$where .= " OR tfrom_user LIKE '%{$row['from_user']}%'";
		}
	}
	if (!empty($_GPC['isdel'])) {
		if ($_GPC['isdel'] == -1) {
			$isdel = 0;
		} else {
			$isdel = 1;
		}
		$where .= "AND is_del =" . $isdel;
	}
	$now = time();
	$starttime = empty($_GPC['time']['start']) ? strtotime(date("Y-m-d H:i", $now - 2592000)) : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? strtotime(date("Y-m-d H:i", $now + 86400)) : strtotime($_GPC['time']['end']);
	if (!empty($starttime) && !empty($endtime)) {
		$where .= " AND createtime >= " . $starttime;
		$where .= " AND createtime < " . $endtime;
	}

	if (!empty($from_user)) {
		$where .= " AND `from_user` = '{$from_user}'";
	}
	if (!empty($tfrom_user)) {
		$where .= " AND `tfrom_user` = '{$tfrom_user}'";
	}
	if (!empty($afrom_user)) {
		$where .= " AND `afrom_user` = '{$afrom_user}'";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$votelogs = pdo_fetchall('SELECT * FROM ' . tablename($this -> table_log) . ' WHERE `rid` = ' . $rid . ' ' . $where . $uni . '   order by `createtime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);

	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this -> table_log) . ' WHERE `rid` = ' . $rid . ' ' . $where . $uni . '  order by `createtime` desc ');
	$pager = pagination($total, $pindex, $psize);

} elseif ($op == 'loupiao') {

	if ($_GPC['foo'] == 'huifu') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 100;
		$where = "";
		$where .= " AND (transid != '' OR transid <> '0') AND (paytime != '' OR paytime != '0') ";
		$where .= " AND ispayvote > 1";
		$where .= " AND paytype < 6";

		$votelogs = pdo_fetchall('SELECT * FROM ' . tablename($this -> table_order) . ' WHERE `rid` = ' . $rid . ' ' . $where . $uni . '   order by `createtime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
		if (!empty($votelogs)) {
			foreach ($votelogs as $key => $value) {
				$tfrom_user = $value['tfrom_user'];
				$vote_times = $value['vote_times'];
				$user = $this -> _getloguser($rid, $value['from_user']);
				$votedate = array('uniacid' => $uniacid, 'rid' => $rid, 'tptype' => '3', 'vote_times' => $vote_times, 'avatar' => $user['avatar'], 'nickname' => $user['nickname'], 'from_user' => $value['from_user'], 'afrom_user' => $value['fromuser'], 'tfrom_user' => $tfrom_user, 'ordersn' => $value['ordersn'], 'islp' => '1', 'ip' => $value['ip'], 'iparr' => $value['iparr'], 'createtime' => $value['paytime']);
				pdo_insert($this -> table_log, $votedate);
				pdo_update($this->table_order, array('ispayvote' => '1'), array('ordersn' => $value['ordersn']));
				$user = pdo_fetch("SELECT hits,photosnum FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $tfrom_user, ':rid' => $rid));
				pdo_update($this -> table_users, array('photosnum' => $user['photosnum'] + $vote_times, 'hits' => $user['hits'] + $vote_times), array('rid' => $rid, 'from_user' => $tfrom_user));
				$rdisplay = pdo_fetch("SELECT ljtp_total,cyrs_total  FROM " . tablename($this -> table_reply_display) . " WHERE rid = :rid", array(':rid' => $rid));
				pdo_update($this -> table_reply_display, array('ljtp_total' => $rdisplay['ljtp_total'] + $vote_times, 'cyrs_total' => $rdisplay['cyrs_total'] + $vote_times), array('rid' => $rid));
				//增加总投票 总人气

			}
			$page = $pindex + 1;
			$msg .= '正在恢复中，目前恢复：<strong style="color:#5cb85c">' . $psize * $pindex . ' 次</strong>';

			$to = $this -> createWebUrl('votelog', array('op' => 'loupiao', 'foo' => 'huifu', 'toi' => $toi, 'page' => $page, 'rid' => $rid));
			message($msg, $to);
		} else {
			$msg .= '恢复成功！';
			$to = $this -> createWebUrl('votelog', array('op' => 'loupiao', 'rid' => $rid));
			message($msg, $to);
		}

	} else {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$where = "";
		$where .= " AND (transid != '' OR transid <> '0') AND (paytime != '' OR paytime != '0') ";
		if (!empty($_GPC['ispayvote'])) {
			if ($_GPC['ispayvote'] == 1) {
				$where .= " AND ispayvote = 1";
			} else {
				$where .= " AND ispayvote > 1";
			}
		}else{
			$where .= " AND ispayvote > 0";
		}

		$where .= " AND paytype < 6";
		$votelogs = pdo_fetchall('SELECT * FROM ' . tablename($this -> table_order) . ' WHERE `rid` = ' . $rid . ' ' . $where . $uni . '   ORDER BY `createtime` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this -> table_order) . ' WHERE `rid` = ' . $rid . ' ' . $where . $uni . '  order by `createtime` desc ');
		$pager = pagination($total, $pindex, $psize);
	}

}
include $this -> template('web/votelog');
