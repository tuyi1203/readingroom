<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

		$reply = pdo_fetch("select * from ".tablename($this->table_reply)." where rid = :rid", array(':rid' => $rid));
        if (empty($reply)) {
            $this->webmessage('抱歉，要操作的活动不存在或是已经被删除！');
        }

		if ($_GPC['type'] == 'del') {
			$ids = implode(',',$_GPC['idArr']);
			$uploadArr = pdo_fetchall("select id,pic from ".tablename('fm_photosvote_wm_upload_value')." where FIND_IN_SET(id,'{$ids}') and rid = :rid", array(':rid' => $rid));

			 foreach ($uploadArr as $k => $v) {
				//删除图片记录
				unlink($v['pic']);
				//删除上传记录
				pdo_delete('fm_photosvote_wm_upload_value', array('id' => $v['id']));
	        }
			$this->webmessage('用户上传删除成功！', '', 0);
		}else if ($_GPC['type'] == 'status') {
			$date = array('status' => $_GPC['status']);
			pdo_update('fm_photosvote_wm_upload_value',$date, array('id' => $_GPC['id']));
			message('用户上传审核/取消审核成功！', referer(), 'success');
		}


