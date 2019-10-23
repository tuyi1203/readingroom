<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

$advs = pdo_fetchall("SELECT link, thumb, times FROM " . tablename($this->table_advs) . " WHERE ismiaoxian = 1 AND issuiji = 1 AND rid= '{$rid}' ORDER BY displayorder ASC");
$advarr = array();
foreach ($advs as $mid => $adv) {
	
	$advarr['link'.$mid] .= $adv['link'];
	if (!$advarr['link'.$mid]) {
		$advarr['link'.$mid] = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvote', array('rid' => $rid));
	}
	$advarr['thumb'.$mid] .= $adv['thumb'];
	$advarr['times'.$mid] .= $adv['times'];
}
$totaladvs = count($advs)-1;
$sjmid = rand(0,$totaladvs);


$toye = $this->_stopllq('miaoxian');
include $this->template($toye);
		