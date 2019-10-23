<?php
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$pindex = max(1, intval($_GPC['page']));
$psize = 10;
$categorys = pdo_fetchall("SELECT * FROM ".tablename('wxz_wzb_category')." WHERE uniacid=:uniacid ORDER BY sort ASC,dateline DESC  LIMIT ".($pindex - 1) * $psize.",{$psize}",array(':uniacid'=>$uniacid));
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wxz_wzb_category') . " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
$pager = pagination($total, $pindex, $psize);
include $this->template('category_list');