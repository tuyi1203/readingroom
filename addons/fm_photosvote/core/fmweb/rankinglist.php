<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
$op = $_GPC['op'];
$indexpx = intval($_GPC['indexpx']);
$indexpxf = intval($_GPC['indexpxf']);
if (empty($page)) {
	$page = 1;
}
$where = '';
$now = time();
$starttime = empty($_GPC['time']['start']) ? strtotime(date("Y-m-d H:i", $now - 2592000)) : strtotime($_GPC['time']['start']);
$endtime = empty($_GPC['time']['end']) ? strtotime(date("Y-m-d H:i", $now)) : strtotime($_GPC['time']['end']);
if (!empty($starttime) && !empty($endtime)) {
	$where .= " AND createtime >= " . $starttime;
	$where .= " AND createtime < " . $endtime;
}
$tagid = !empty($_GPC['category']['childid']) ? $_GPC['category']['childid'] : $_GPC['tagid'];
$tagpid = !empty($_GPC['category']['parentid']) ? $_GPC['category']['parentid'] : $_GPC['tagpid'];
$tagtid = !empty($_GPC['category']['threecs']) ? $_GPC['category']['threecs'] : $_GPC['tagtid'];
$tags = pdo_fetchall("SELECT * FROM " . tablename($this -> table_tags) . " WHERE rid = :rid " . $uni . " ORDER BY id DESC", array(':rid' => $rid));
$tagname = $this -> gettagname($tagid, $tagpid, $tagtid, $rid);
if ($op == 'tags') {
	if (!empty($tagid)) {
		$where .= " AND parentid = " . $tagid;
	}elseif (!empty($tagpid)) {
		$tagids = pdo_fetchall("SELECT id FROM ".tablename($this->table_tags)." WHERE rid = :rid AND parentid = :parentid ORDER BY id DESC", array( ':rid' => $rid, ':parentid' => $tagpid));
		$where .= " AND ( ";
		foreach ($tagids as $key => $row) {
			if ($key > 0) {
				$where .= " OR parentid = " . $row['id'];
			}else{
				$where .= " parentid = " . $row['id'];
			}

		}
		$where .= " ) ";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$order = '';

	$list_praise = pdo_fetchall("SELECT id, title FROM ".tablename($this->table_tags)." WHERE rid = :rid AND icon = 3 ".$where. $uni ." ORDER BY id DESC  LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));
	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_tags).' WHERE rid = :rid AND icon = 3 '.$where. $uni .'', array(':rid' => $rid));
	//$total_pages = ceil($total/$psize);
	foreach ($list_praise as $key => $tag) {
		$photosnum = pdo_fetchcolumn('SELECT SUM(photosnum) FROM '.tablename($this->table_users).' WHERE rid = :rid AND tagtid = :tagtid AND status = 1', array(':rid' => $rid,':tagtid' => $tag['id']));
		$xnphotosnum = pdo_fetchcolumn('SELECT SUM(xnphotosnum) FROM '.tablename($this->table_users).' WHERE rid = :rid AND tagtid = :tagtid  AND status = 1', array(':rid' => $rid,':tagtid' => $tag['id']));
		$list_praise[$key]['piaoshu'] .= $photosnum + $xnphotosnum;
	}
	sortArrByField($list_praise,'piaoshu');

	//取得用户列表
	//$list_praise = pdo_fetchall('SELECT * FROM ' . tablename($this -> table_users) . ' WHERE rid= :rid ' . $where . $uni . ' order by ' . $order . ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));
	//$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this -> table_users) . ' WHERE rid= :rid ' . $where . $uni . ' ', array(':rid' => $rid));
	$pager = pagination($total, $pindex, $psize);
}else{


	if (!empty($tagid)) {
		$where .= " AND tagid = '" . $tagid . "'";
	}
	if (!empty($tagpid)) {
		$where .= " AND tagpid = '" . $tagpid . "'";
	}
	if (!empty($tagtid)) {
		$where .= " AND tagtid = '" . $tagtid . "'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$order = '';
	//0 按最新排序 1 按人气排序 3 按投票数排序
	if ($indexpx == '-1') {
		$order .= " `createtime` DESC";
	} elseif ($indexpx == '1') {
		$order .= " `hits` + `xnhits` DESC";
	} elseif ($indexpx == '2') {
		$order .= " `photosnum` + `xnphotosnum` DESC";
	}

	//0 按最新排序 1 按人气排序 3 按投票数排序  倒叙
	if ($indexpxf == '-1') {
		$order .= " `createtime` ASC";
	} elseif ($indexpxf == '1') {
		$order .= " `hits` + `xnhits` ASC";
	} elseif ($indexpxf == '2') {
		$order .= " `photosnum` + `xnphotosnum` ASC";
	}

	if (empty($indexpx) && empty($indexpxf)) {
		$order .= " `photosnum` + `xnphotosnum` DESC";
	}

	//取得用户列表
	$list_praise = pdo_fetchall('SELECT * FROM ' . tablename($this -> table_users) . ' WHERE rid= :rid ' . $where . $uni . ' order by ' . $order . ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));
	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this -> table_users) . ' WHERE rid= :rid ' . $where . $uni . ' ', array(':rid' => $rid));
	$pager = pagination($total, $pindex, $psize);
}
include $this -> template('web/rankinglist');
