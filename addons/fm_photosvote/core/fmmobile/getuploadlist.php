<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

$fmdata = array(
	"success" => -1,
	"msg" => '暂无数据',
);
$filed = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_wm_upload')." WHERE rid = :rid LIMIT 1", array(':rid' => $rid));
if($filed){
	$pagecount = $filed['pagecount'];
	$page = intval($_GPC['page']);
	if( $page == '0'){
		$page = '1';
	}

	$count = pdo_fetchcolumn("SELECT COUNT(id) FROM ".tablename('fm_photosvote_wm_upload_value')." WHERE status=1 and rid = :rid and tfrom_user = :tfrom_user", array(':rid' => $rid,':tfrom_user' => $tfrom_user));
	$sumPage = ceil($count/$pagecount);
	
	$offset = $pagecount*($page-1);
	
	$value = pdo_fetchall("SELECT * FROM ".tablename('fm_photosvote_wm_upload_value')." WHERE status=1 and rid = :rid and tfrom_user = :tfrom_user limit $offset,$pagecount", array(':rid' => $rid,':tfrom_user' => $tfrom_user));
	if( $value){
		$fmdata = array(
			"success" => 1,
			"sumpage" => $sumPage,
			"msg" => '查询成功',
			"data"=>$value,
		);
	}
}
echo json_encode($fmdata);
exit();