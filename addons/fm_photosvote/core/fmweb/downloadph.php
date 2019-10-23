<?php
/**
 * 女神来了导出
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
global $_GPC,$_W;
$rid= intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
$indexpx = intval($_GPC['indexpx']);
$indexpxf = intval($_GPC['indexpxf']);
$tagid = $_GPC['tagid'];
$tagpid = $_GPC['tagpid'];


if(empty($rid)){
    message('抱歉，传递的参数错误！','', 'error');              
}

if ($_GPC['uni_all_users'] != 1) {
	if ($uniacid != $_GPC['uniacid']) {
		$uni = " AND uniacid = ".$uniacid;
	}
}
if (!empty($tagid)) {
	$where .= " AND tagid = '".$tagid."'";
}elseif (!empty($tagpid)) {
	$where .= " AND tagpid = '".$tagpid."'";
}
$where = '';
$order = '';
$starttime = $_GPC['start_time'];
$endtime = $_GPC['end_time'];
if (!empty($starttime) && !empty($endtime)) {
	$where .= " AND createtime >= " . $starttime; 
	$where .= " AND createtime < " . $endtime; 
}

//0 按最新排序 1 按人气排序 3 按投票数排序
if ($indexpx == '-1') {
	$order .= " `createtime` DESC";
}elseif ($indexpx == '1') {
	$order .= " `hits` + `xnhits` DESC";
}elseif ($indexpx == '2') {
	$order .= " `photosnum` + `xnphotosnum` DESC";
}

//0 按最新排序 1 按人气排序 3 按投票数排序  倒叙
if ($indexpxf == '-1') {
	$order .= " `createtime` ASC";
}elseif ($indexpxf == '1') {
	$order .= " `hits` + `xnhits` ASC";
}elseif ($indexpxf == '2') {
	$order .= " `photosnum` + `xnphotosnum` ASC";
}
if (empty($indexpx) && empty($indexpxf)) {
	$order .= " `photosnum` + `xnphotosnum` DESC";
}
$list = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE rid =:rid '.$where.$uni.' ORDER BY '.$order.' ', array(':rid' => $rid));	

$tableheader = array('用户ID', '排名', '姓名','分组', '手机号', '宣言', '真实票数', '虚拟票数', '真实人气', '虚拟人气', '分享数', '点赞', '评论', '活跃等级', '报名时间');
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $value) {
	$html .= $value . "\t ,";
}
$html .= "\n";
foreach ($list as $mid => $value) {
	$sharenum = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_data)." WHERE tfrom_user = :tfrom_user and rid = :rid", array(':tfrom_user' => $value['from_user'],':rid' => $rid));
	if(empty($value['realname'])){
		$username = $value['nickname'];
	}else {
		$username = $value['realname'];
	}
	$remsg = $this->getcommentnum($rid, $uniacid, $value['from_user']);
	$level = intval($this->fmvipleavel($rid, $uniacid, $value['from_user']));
	$tagname = $this->gettagname($value['tagid'],$value['tagpid'],$rid);	
	$p = $mid + 1;
	$html .= $value['uid'] . "\t ,";	
	$html .= $p . "\t ,";	
	$html .= $username . "\t ,";	
	$html .= $tagname . "\t ,";	
	$html .= $value['mobile'] . "\t ,";	
	$html .= $value['photoname'] . "\t ,";
	$html .= $value['photosnum'] . "\t ,";	
	$html .= $value['xnphotosnum'] . "\t ,";	
	$html .= $value['hits'] . "\t ,";
	$html .= $value['xnhits'] . "\t ,";	
	$html .= $sharenum . "\t ,";			
	$html .= $value['zans'] . "\t ,";			
	$html .= $remsg . "\t ,";		
	$html .= $level . "\t ,";
	$html .= date('Y年m月d日 H:i:s',$value['createtime']) . "\t ,";
	$html .= "\n";
}
$filename = $_GPC['title'].'_排行榜_'.$rid.'_'.$now;

header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=".$filename.".csv");

echo $html;
exit();
