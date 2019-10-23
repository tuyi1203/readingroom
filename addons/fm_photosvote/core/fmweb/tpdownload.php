<?php
/**
 * 女神来了导出
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
global $_GPC,$_W;
$rid= intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
if(empty($rid)){
    message('抱歉，传递的参数错误！','', 'error');
}
	$afrom_user = $_GPC['afrom_user'];
	$tfrom_user = $_GPC['tfrom_user'];
	$keyword = $_GPC['keyword'];

	if ($_GPC['uni_all_users'] != 1) {
		if ($uniacid != $_GPC['uniacid']) {
			$uni = " AND uniacid = ".$uniacid;
		}
	}

	$where = "";
	$starttime = $_GPC['start_time'];
	$endtime = $_GPC['end_time'];
	if (!empty($starttime) && !empty($endtime)) {
		$where .= " AND createtime >= " . $starttime;
		$where .= " AND createtime < " . $endtime;
	}
	if (!empty($keyword)){
			$where .= " AND nickname LIKE '%{$keyword}%'";
			$where .= " OR ip LIKE '%{$keyword}%'";
			$t = pdo_fetchall("SELECT from_user FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and  rid = :rid and nickname LIKE '%{$keyword}%' ", array(':uniacid' => $uniacid, ':rid' => $rid));
			foreach ($t as $row) {
				$where .= " OR tfrom_user LIKE '%{$row['from_user']}%'";
			}
	}

	if (!empty($tfrom_user)){
	$where .= " AND `tfrom_user` = '{$tfrom_user}'";
	}
	if (!empty($afrom_user)){
		$where .= " AND `afrom_user` = '{$afrom_user}'";
	}

	$list = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE rid= :rid '.$where.$uni.'  ORDER BY `createtime` ASC', array(':rid' => $rid) );

if ($_GPC['type'] == 1) {

	$tableheader = array('id' => 'ID', 'username' => '投票人', 'realname' => '真实姓名', 'mobile' => '手机号', 'vote_times' => '投票数', 'votetype' => '投票是否付费', 'votefrom' => '投票来源', 'fnickname' => '被投票人', 'fmobile' => '联系方式', 'anickname' => '拉票人', 'ip' => '投票IP', 'country' => '投票国家', 'province' => '投票城市', 'city' => '投票地区', 'status' => '状态', 'spstatus' => '封禁状态', 'createtime' => '投票时间');

$keys = array_keys($tableheader);
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $li) {
	$html .= $li . "\t ,";
}
$html .= "\n";

if (!empty($list)) {
	$size = ceil(count($list) / 500);

	for ($i = 0; $i < $size; $i++) {
		$buffer = array_slice($list, $i * 500, 500);

		foreach($buffer as $value) {
			$fuser = $this->_getuser($value['rid'], $value['tfrom_user']);
			$auser = $this->_auser($value['rid'], $value['afrom_user']);
			$iparr = iunserializer($value['iparr']);
			$tpinfo = $this->gettpinfo($rid,$value['from_user']);
			$value['username'] = $this->getname($rid, $value['from_user']);
			$value['votetype'] = (!empty($value['ordersn'])) ? '付费投票' : '免费投票';
			$value['status'] = ($value['is_del'] == 1) ? '无效票（用户取消关注）' : '正常';
			$value['spstatus'] = ($value['shuapiao'] == 1) ? '已封禁' : '未封禁';
			if ($value['tptype'] == 1){
				$value['status'] = '网页投票';
			}elseif ($value['tptype'] == 2){
				$value['status'] = '会话界面';
			}elseif ($value['tptype'] == 3){
				$value['status'] = '微信支付';
			}else{
				$value['status'] = '其他';
			}
			$value['realname'] = $tpinfo['realname'];
			$value['mobile'] = $tpinfo['mobile'];
			$value['fnickname'] = $fuser['realname'];
			$value['fmobile'] = $fuser['mobile'];
			if (is_array($iparr)) {
				$value['country'] = $iparr['country'];
				$value['province'] = $iparr['province'];
				$value['city'] = $iparr['city'];
			}else{
				$value['city'] = str_replace(',', ' | ', $value['iparr']);
			}
			$value['anickname'] = $auser['nickname'];
			$value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
			foreach($keys as $key) {
				$data[] = $value[$key];
			}
			$user[] = implode("\t ,", $data) . "\t ,";
					unset($data);
		}
	}
	$html .= implode("\n", $user);
}
$now = date('Y-m-d H:i:s', time());
if ($keyword) {
	$k = $keyword.' 的';
}
$filename =$k.'投票记录情况'.'_'.$rid.'_'.$now;

//$filename = $_GPC['title'] . '_' . $rid . '_' . $pindex;
header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=" . $filename . ".csv");
echo $html;
exit();

}else{

	$vote = pdo_fetchall("SELECT distinct(ip) FROM ".tablename($this->table_log)." WHERE rid = :rid ".$where.$uni."  ", array(':rid' => $rid));

	$tvtotal = array();
	foreach ($vote as $v) {
		$total = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_log)." WHERE rid= :rid AND ip = :ip ".$where.$uni."   order by `ip` desc ", array(':rid' => $rid, ':ip' => $v['ip']));
		$tvtotal[$v[ip]] .= $total;
	}
	arsort($tvtotal);
		$html .= '统计情况' . "\t ,";
		$html .= '排行' . "\t ,";
		$html .= '相同IP' . "\t ,";
		$html .= '地区' . "\t ,";
		$html .= '投票次数' . "\t ,";
		$html .= "\n";
		$n = 0;

	foreach ($tvtotal as $mid => $t) {
		$ip = GetIpLookup($mid);
		$ip = $ip['country'].'  '.$ip['province'].'  '.$ip['city'].'  '.$ip['district'].'  '.$ip['ist'];
		$html .= '' . "\t ,";
		$html .= $n +1 . "\t ,";
		$html .= $mid . "\t ,";
		$html .= $ip . "\t ,";
		$html .= $t . '次' . "\t ,";
		$html .= "\n";
		$n++;
	}


	$now = date('Y-m-d H:i:s', time());
	if ($keyword) {
		$k = $keyword.' 的';
	}
	$filename =$k.'投票ip统计情况'.'_'.$rid.'_'.$now;

	header("Content-type:text/csv");
	header("Content-Disposition:attachment; filename=".$filename.".csv");

	echo $html;
	exit();
}
