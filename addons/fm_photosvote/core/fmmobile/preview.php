<?php

global $_W, $_GPC;
$pageid = $_GPC['pageid'];
if (!empty($pageid)) {
	$page = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $pageid));
}else{
	if ($_GPC['do'] == 'photosvote') {
		$pagetype = 1;
	}elseif ($_GPC['do'] == 'tuser') {
		$pagetype = 2;
	}elseif ($_GPC['do'] == 'paihang') {
		$pagetype = 3;
	}elseif ($_GPC['do'] == 'reg') {
		$pagetype = 4;
	}elseif ($_GPC['do'] == 'des') {
		$pagetype = 5;
	}
	$page = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid AND stylename =:stylename AND pagetype=:pagetype ", array(':uniacid' => $_W['uniacid'], ':stylename' => $rbasic['templates'], ':pagetype' => $pagetype));
}
$pagedata = $this->getData($page);
extract($pagedata);
$guide = $this->getGuide($system, $pageinfo);

$_W['shopshare'] = array('title' => $share['title'], 'imgUrl' => $share['imgUrl'], 'desc' => $share['desc'], 'link' => $sharelink);

include $this->template('preview/index');