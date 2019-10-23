<?php

global $_W,$_GPC;
$uniacid = $_W['uniacid'];
load()->func('tpl');
$op = empty($_GPC['op'])? 'list':$_GPC['op'];
$rid = intval($_GPC['rid']);
if(empty($rid)){
	message('直播id不存在',$this->createWebUrl('list_manage'),'error');
}
$zhibo_list = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_live_setting')." WHERE uniacid=:uniacid AND rid=:rid",array(':uniacid'=>$uniacid,':rid'=>$rid));
if(empty($zhibo_list)){
	message('此直播不存在或是已经被删除',$this->createWebUrl('live_manage'),'error');
}
if($op=='list'){
	$menus = pdo_fetchall("SELECT * FROM ".tablename('wxz_wzb_list_menu')." WHERE uniacid=:uniacid AND rid=:rid ORDER BY displayorder ASC,createtime DESC",array(':uniacid'=>$uniacid,':rid'=>$rid));
}elseif($op=='post'){
	$id = intval($_GPC['id']);
	$menu = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_list_menu')." WHERE uniacid=:uniacid AND rid=:rid",array(':uniacid'=>$uniacid,':rid'=>$rid));
	if(empty($menu)){
		$menu = array(
			'type'=>'html',
			'isshow'=>1,
			'displayorder'=>0,
		);
		$menu['settings']['news_zan'] = 0;
		$menu['settings']['host_pass'] = '12345';
	}else{
		$menu['settings'] = iunserializer($menu['settings']);
	}
	if (checksubmit('submit')) {
		$data = array();
		$type = $_GPC['menu_type'];
		$menu_type = array('html','content','comment','button','shake','news');
		if(!in_array($type,$menu_type)){
			message('请先选择菜单类型');
		}else{
			$post_setting = array();
			if($type=='html'){
				$post_setting['iframe'] = $_GPC['iframe'];
			}elseif($type=='content'){
				$post_setting['content'] = $_GPC['menu_content'];
			}elseif($type=='comment'){
				
				$post_setting['comment_zan'] = $_GPC['comment_zan'];
				$post_setting['comment_pinglun'] = $_GPC['comment_pinglun'];
			}elseif($type=='button'){
				$post_setting['button_name'] = $_GPC['button_name'];
				$post_setting['button_url'] = $_GPC['button_url'];
			}elseif($type=='shake'){
				$post_setting['shake_times'] = intval($_GPC['shake_times']);
				$post_setting['get_one_award'] = intval($_GPC['get_one_award']);
			}elseif($type=='news'){
				$post_setting['host_img'] = $_GPC['host_img'];
				$post_setting['host_name'] = $_GPC['host_name'];
				$post_setting['host_pass'] = $_GPC['host_pass'];
				$post_setting['news_zan'] = intval($_GPC['news_zan']);
			}				
		}
		$data['type'] = $type;
		$data['rid'] = $rid;
		$data['isshow'] = intval($_GPC['isshow']);
		$data['displayorder'] = intval($_GPC['displayorder']);
		$data['name'] = $_GPC['name'];
		$data['uniacid'] = $uniacid;
		$data['settings'] = iserializer($post_setting);

		if(!empty($id)){
			pdo_update('wxz_wzb_list_menu',$data,array('id'=>$id,'uniacid'=>$uniacid));
			message('编辑成功',$this->createWebUrl('list_menu',array('rid'=>$rid)),'success');
		}else{
			$data['createtime'] = time();
			pdo_insert('wxz_wzb_list_menu',$data);
			message('新增成功',$this->createWebUrl('list_menu',array('rid'=>$rid)),'success');
		}

	}
}elseif($op=='del'){
	$rid = intval($_GPC['rid']);
	$list = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_list_menu')." WHERE uniacid=:uniacid AND rid=:rid",array(':uniacid'=>$uniacid,':rid'=>$rid));
	if(empty($list)){
		message('此项不存在或是已经被删除',$this->createWebUrl('list_menu',array('rid'=>$rid)),'error');
	}else{
		pdo_delete('wxz_wzb_list_menu',array('rid'=>$rid,'uniacid'=>$uniacid));
	}
	message('删除成功',$this->createWebUrl('list_menu',array('rid'=>$rid)),'success');
}
include $this->template('list_menu');