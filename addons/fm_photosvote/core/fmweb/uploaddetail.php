<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');
/*
		$rdisplay = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$rvote = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$reply = array_merge($rdisplay, $rvote);
		$regtitlearr = iunserializer($rdisplay['regtitlearr']);
		$foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';
		$now = time();
*/
		$uploadTable = '';
		$upload = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_wm_upload')." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $_GPC['rid']));
		$item = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_wm_upload_value')." WHERE id = :id", array(':id' => $_GPC['id']));
		
		include $this->template('web/uploaddetail');
