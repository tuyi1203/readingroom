<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
if (!empty($rid)) {
	pdo_delete($this->table_bbsreply, " rid = ".$rid);
	message('删除成功！', referer(),'success');
}		
		