<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

//print_r($_GPC["payyz"]);

$item = pdo_fetch("SELECT * FROM " . tablename($this -> table_order) . " WHERE ordersn='{$_GPC['ordersn']}' limit 1");

if ($item['status'] == 1 && $item['payyz'] == $_GPC['payyz'] && !empty($item['transid'])) {
	if ($_COOKIE["user_charge_payyz"] != $_GPC['payyz']) {
		if (!empty($_COOKIE["user_charge_payyz"])) {
			setcookie("user_charge_payyz", '', time() - 1);
		}
		$remark = '微信充值，<span class="label label-warning">增加</span>'.$_GPC['jifen'].'积分';
		$this->addorderlog($rid, $_GPC['ordersn'], $from_user, $_GPC['jifen'], '积分充值', $type = '3', $remark);
		$this -> addjifencharge($_GPC['rid'], $_GPC['from_user'], $_GPC['jifen'], $_GPC['ordersn']);

		setcookie("user_charge_payyz", $_GPC['payyz'], time() + 3600 * 24);
	}
}
$templatename = $rbasic['templates'];
$toye = $this -> templatec($templatename, $_GPC['do']);
include $this -> template($toye);
