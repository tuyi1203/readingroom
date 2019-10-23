<?php
/**
* 女神来了模块定义
*
* @author 幻月科技
* @url http://bbs.fmoons.com/
*/
defined('IN_IA') or exit('Access Denied');
$base = pdo_fetch('SELECT * FROM '.tablename($this->table_reply).' WHERE rid =:rid ', array(':rid' => $rid) );
$vote = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid", array(':rid' => $rid));
$reply = array_merge($base, $vote);
$rdisplay = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
$regtitlearr = iunserializer($rdisplay['regtitlearr']);
if (checksubmit('submitdr')) {
	if($_GPC['leadExcel'] == "true") {
		$filename = $_FILES['inputExcel']['name'];
		$tmp_name = $_FILES['inputExcel']['tmp_name'];

		$msg = $this->uploadFile($filename,$tmp_name,$rid);
		message($msg,referer(),'success');
	}
}


if (checksubmit('delete')) {
	pdo_delete($this->table_users, " id IN ('".implode("','", $_GPC['select'])."')");
	message('删除成功！', create_url('site/module', array('do' => 'members', 'name' => 'fm_photosvote', 'rid' => $rid, 'page' => $_GPC['page'], 'foo' => 'display')));
}
$where = '';
if (!empty($_GPC['keyword'])) {
	$keyword = $_GPC['keyword'];
	$where .= " AND (uid LIKE '%{$keyword}%' OR nickname LIKE '%{$keyword}%' OR realname LIKE '%{$keyword}%' OR mobile LIKE '%{$keyword}%' OR photoname LIKE '%{$keyword}%') ";

}
$now = time();
$starttime = empty($_GPC['time']['start']) ?  strtotime(date("Y-m-d H:i", $now - 604799)) : strtotime($_GPC['time']['start']);
$endtime = empty($_GPC['time']['end']) ?  strtotime(date("Y-m-d H:i", $now+86400)) : strtotime($_GPC['time']['end']);
if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
	$where .= " AND createtime >= " . $starttime;
	$where .= " AND createtime < " . $endtime;
}
$where .= " AND status = '1'";

$pindex = max(1, intval($_GPC['page']));
$psize = 15;

//取得用户列表
$members = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE rid = :rid '.$where.$uni.' order by `uid` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid) );
$total = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_users).' WHERE rid = :rid  '.$where.$uni.' ', array(':rid'=>$rid));
$pager = pagination($total, $pindex, $psize);
$sharenum = array();
foreach ($members as $mid => $m) {
	$sharenum[$mid] = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_data)." WHERE tfrom_user = :tfrom_user and rid = :rid ".$uni." ", array(':tfrom_user' => $m['from_user'],':rid' => $rid)) + pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_data)." WHERE fromuser = :fromuser and rid = :rid ".$uni." ", array(':fromuser' =>$m['from_user'], ':rid' => $rid)) + $m['sharenum'];
}

include $this->template('web/members');
