<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');

	/*$rdisplay = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
	$rvote = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
	$reply = array_merge($rdisplay, $rvote);
	$regtitlearr = iunserializer($rdisplay['regtitlearr']);
	$foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';
	$now = time();*/

$pindex = max(1, intval($_GPC['page']));
$psize = 15;

//取得用户列表
$list_praise = pdo_fetchall('SELECT v.id,v.status,p.avatar,p.nickname,v.head,v.nickname AS unickname,v.status,v.pic,v.time FROM ims_fm_photosvote_wm_upload_value AS v LEFT JOIN ims_fm_photosvote_provevote AS p ON p.from_user=v.tfrom_user WHERE v.rid=:rid order by v.id desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid) );

//取得用户列表
$total = pdo_fetchcolumn("SELECT COUNT(1) FROM ims_fm_photosvote_wm_upload_value AS v LEFT JOIN ims_fm_photosvote_provevote AS p ON p.from_user=v.tfrom_user WHERE v.rid=:rid", array(':rid'=>$rid));
$pager = pagination($total, $pindex, $psize);


include $this->template('web/upload');
