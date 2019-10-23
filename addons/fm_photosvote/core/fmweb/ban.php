<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

$fmswich = $_GPC['fmswich'];
$ban = $_GPC['ban'];
$sqltype = $_GPC['sqltype'];
if (empty($fmswich)) {
    message('请设置功能', referer(), 'error');
}
$date = array();
$date[$fmswich] = $ban;
pdo_update($sqltype,$date,array('rid'=>$_GPC['rid']));


message($_GPC['rid'], '', 'ajax');
