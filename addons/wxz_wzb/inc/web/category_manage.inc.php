<?php
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$op = empty($_GPC['op'])? 'list':$_GPC['op'];
if($op=='list'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$categorys = pdo_fetchall("SELECT * FROM ".tablename('wxz_wzb_category')." WHERE uniacid=:uniacid ORDER BY displayorder ASC,createtime DESC  LIMIT ".($pindex - 1) * $psize.",{$psize}",array(':uniacid'=>$uniacid));
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wxz_wzb_category') . " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	$pager = pagination($total, $pindex, $psize);
}elseif($op=='post'){
	load()->func('tpl');
	$id = intval($_GPC['id']);
	$category = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_category')." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$uniacid,':id'=>$id));
	if(empty($category)){
		$category = array(
			'isshow'=>1,
		);
	}
	if (checksubmit('submit')) {
			
		$data = array();
		$data['uniacid'] = $uniacid;
		$data['title'] = $_GPC['title'];
		$data['isshow'] = intval($_GPC['isshow']);
		$data['no_img'] = $_GPC['no_img'];
		$data['displayorder'] = intval($_GPC['displayorder']);
		if(!empty($id)){
			pdo_update('wxz_wzb_category',$data,array('id'=>$id,'uniacid'=>$uniacid));
			message('编辑成功',$this->createWebUrl('category_manage'),'success');
		}else{
			$data['createtime'] = time();
			pdo_insert('wxz_wzb_category',$data);
			message('新增成功',$this->createWebUrl('category_manage'),'success');
		}
	}
}elseif($op=='del'){
	$id = intval($_GPC['id']);
	$category = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_category')." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$uniacid,':id'=>$id));
	if(empty($category)){
		message('此项不存在或是已经被删除',referer());
	}else{
		$live_lists = pdo_fetchall("SELECT `id` FROM ".tablename('wxz_wzb_live_setting')." WHERE uniacid=:uniacid AND categoryid=:categoryid",array(':uniacid'=>$uniacid,':categoryid'=>$id));
		if(!empty($live_lists) && is_array($live_lists)){
			foreach($live_lists as $row){
				pdo_delete($this->pinglun_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->zan_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->share_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->list_user_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->listmenu_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->gift_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->shake_record_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete($this->shake_award_table,array('listid'=>$row['id'],'uniacid'=>$uniacid));
				pdo_delete('wxz_wzb_live_setting',array('id'=>$row['id'],'uniacid'=>$uniacid));
			}
		}
		pdo_delete('wxz_wzb_category',array('id'=>$id,'uniacid'=>$uniacid));
	}
	message('删除成功',referer());
}
include $this->template('category_manage');