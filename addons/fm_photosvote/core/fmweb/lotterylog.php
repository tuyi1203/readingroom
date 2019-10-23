<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');

$pindex = max(1, intval($_GPC['page']));
$psize = 15;

//取得用户列表
$list_praise = pdo_fetchall("SELECT l.*,u.name,u.tel FROM ".tablename('fm_photosvote_wm_lottery_log')." AS l LEFT JOIN ".tablename('fm_photosvote_wm_user')." AS u ON l.from_user=u.from_user and l.rid=u.rid WHERE l.rid=:rid AND `name` IS NOT NULL order by l.lid,l.id desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(':rid' => $rid) );

//取得总数
$total = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename('fm_photosvote_wm_lottery_log')." AS l LEFT JOIN ".tablename('fm_photosvote_wm_user')." AS u ON l.from_user=u.from_user and l.rid=u.rid WHERE l.rid=:rid AND `name` IS NOT NULL", array(':rid'=>$rid));
$pager = pagination($total, $pindex, $psize);


include $this->template('web/lotterylog');
