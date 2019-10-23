<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
		$status = $_GPC['status'];
		if ($status == '-1') {
			$title = $rbasic['title'] . ' 即将开始哦 - ';
			$stopbg = toimage($rbasic['nostart']);
		}elseif ($status == '0') {
			$title = $rbasic['title'] . ' 暂停中哦 - ';
			$stopbg = toimage($rbasic['stopping']);
		}elseif ($status == '1') {
			$title = $rbasic['title'] . ' 已经停止了，期待下一次吧！';
			$stopbg = toimage($rbasic['end']);
		}
		$toye = $this->_stopllq('stop');
		include $this->template($toye);
	