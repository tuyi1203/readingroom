<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
load() -> func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$list = pdo_fetchall("SELECT * FROM " . tablename($this -> table_banners) . " WHERE rid = '{$rid}' " . $uni . "  ORDER BY displayorder DESC");
	//	include $this->template('web/banner');
} elseif ($operation == 'post') {

	$id = intval($_GPC['id']);
	if (checksubmit('submit')) {
		if (empty($_GPC['bannername'])) {
			message('请填写标题');
		}
		$data = array('uniacid' => $uniacid, 'rid' => $rid, 'bannername' => $_GPC['bannername'], 'link' => $_GPC['link'], 'thumb' => $_GPC['thumb'], 'enabled' => intval($_GPC['enabled']), 'displayorder' => intval($_GPC['displayorder']));

		if (!empty($id)) {
			pdo_update($this -> table_banners, $data, array('id' => $id));
			load() -> func('file');
			file_delete($_GPC['thumb_old']);
		} else {
			pdo_insert($this -> table_banners, $data);
			$id = pdo_insertid();
		}
		message('更新幻灯片成功！', $this -> createWebUrl('banner', array('op' => 'display', 'rid' => $rid)), 'success');
	}
	$banner = pdo_fetch("select * from " . tablename($this -> table_banners) . " where id=:id and rid=:rid limit 1", array(":id" => $id, ':rid' => $rid));
	//include $this->template('web/banner_post');
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$banner = pdo_fetch("SELECT id  FROM " . tablename($this -> table_banners) . " WHERE id = '$id' AND rid=" . $rid . "");
	if (empty($banner)) {
		message('抱歉，幻灯片不存在或是已经被删除！', $this -> createWebUrl('banner', array('op' => 'display', 'rid' => $rid)), 'error');
	}
	pdo_delete($this -> table_banners, array('id' => $id));
	message('幻灯片删除成功！', $this -> createWebUrl('banner', array('op' => 'display', 'rid' => $rid)), 'success');
} else {
	message('请求方式不存在');
}
include $this -> template('web/banner');
