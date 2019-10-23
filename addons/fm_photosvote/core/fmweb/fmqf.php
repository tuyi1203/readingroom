<?php
/**
 * [Fmoons System] Copyright (c) 2014 FMOONS.COM
 * Fmoons isNOT a free software, it under the license terms, visited http://www.fmoons.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
 $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

	$rid = intval($_GPC['rid']);
if($operation == 'display') {
	$rid = intval($_GPC['rid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = '';
	$params = array();
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND title LIKE :keyword";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
	}
	
	$list = pdo_fetchall("SELECT * FROM ".tablename('site_article')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('site_article') . " WHERE uniacid = '{$_W['uniacid']}'");
	$pager = pagination($total, $pindex, $psize);
	
	include $this->template('web/fmqf');
} elseif($operation == 'post') {
	load()->func('tpl');
	load()->func('file');
	$id = intval($_GPC['id']);
	$rid = intval($_GPC['rid']);
		$template = uni_templates();
	$pcate = $_GPC['pcate'];
	$ccate = $_GPC['ccate'];
	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
		$item['type'] = explode(',', $item['type']);
		$pcate = $item['pcate'];
		$ccate = $item['ccate'];
		if (empty($item)) {
			message('抱歉，文章不存在或是已经删除！', '', 'error');
		}
		$keywords = pdo_fetchcolumn('SELECT content FROM ' . tablename('rule_keyword') . ' WHERE id = :id AND uniacid = :uniacid ', array(':id' => $item['kid'], ':uniacid' => $_W['uniacid']));
		
	} else {
		$item['credit'] = array();
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('标题不能为空，请输入标题！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'iscommend' => intval($_GPC['option']['commend']),
			'ishot' => intval($_GPC['option']['hot']),
			'pcate' => intval($_GPC['category']['parentid']),
			'ccate' => intval($_GPC['category']['childid']),
			'template' => $_GPC['template'],
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'content' => htmlspecialchars_decode($_GPC['content']),
			'incontent' => intval($_GPC['incontent']),
			'source' => $_GPC['source'],
			'author' => $_GPC['author'],
			'displayorder' => intval($_GPC['displayorder']),
			'linkurl' => $_GPC['linkurl'],
			'createtime' => TIMESTAMP,
			'click' => intval($_GPC['click'])
		);
		if (!empty($_GPC['thumb'])) {
			$data['thumb'] = $_GPC['thumb'];
		} elseif (!empty($_GPC['autolitpic'])) {
			$match = array();
			preg_match('/attachment\/(.*?)(\.gif|\.jpg|\.png|\.bmp)/', $_GPC['content'], $match);
			if (!empty($match[1])) {
				$data['thumb'] = $match[1].$match[2];
			}
		} else {
			$data['thumb'] = '';
		}
				
		if (empty($id)) {
			
			pdo_insert('site_article', $data);
			$aid = pdo_insertid();
			
		} else {
			unset($data['createtime']);
			
			pdo_update('site_article', $data, array('id' => $id));
		}
		message('文章更新成功！', $this->createWebUrl('fmqf', array('op' => 'display', 'rid' => $rid)), 'success');
	} else {
		include $this->template('web/fmqf');
	}
}elseif($operation == 'qfusers') {
	$rid = intval($_GPC['rid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = '';
	$params = array();
	$keyword = $_GPC['keyword'];
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND nickname LIKE :keyword";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
	}
	
	$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_qunfa)." WHERE uniacid = '{$_W['uniacid']}' AND rid = '{$rid}' $condition ORDER BY lasttime DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_qunfa) . " WHERE uniacid = '{$_W['uniacid']}'");
	$pager = pagination($total, $pindex, $psize);
	
	include $this->template('web/fmqf');
} elseif($operation == 'fasong') {
	 global $_GPC, $_W;
		if($_W['isajax']) {
				$id = intval($_GPC['id']);
				$rid = intval($_GPC['rid']);
				$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
				$rdisplay = pdo_fetch("SELECT indexpx FROM ".tablename($this->table_reply_display)." WHERE rid = :rid LIMIT 1", array(':rid' => $rid));
				//$groups = pdo_fetchall("SELECT * FROM ".tablename('fm_autogroup_group')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY gname ASC, id DESC ");
				$where = '';
				if ($rdisplay['indexpx'] == '0') {
					$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
				}elseif ($rdisplay['indexpx'] == '1') {
					$where .= " ORDER BY `createtime` DESC";
					
				}elseif ($rdisplay['indexpx'] == '2') {
					$where .= " ORDER BY `hits` + `xnhits` DESC";
				}else{
					$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
				}

				$users = pdo_fetchall("SELECT nickname, realname, uid,photosnum,xnphotosnum FROM ".tablename($this->table_users)." WHERE  rid = '{$rid}' AND status = 1  ".$where." LIMIT 10");
				
				
				include $this->template('web/fasong');
				exit();
		}
	
} elseif($operation == 'fasongstart') {
	 global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$gid = intval($_GPC['gid']);
		$rid = intval($_GPC['rid']);
		$uniacid = $_W['uniacid'];
		if(!$id){
		    message('文章不存在', $this->createWebUrl('fmqf', array('op' => 'display', 'rid' => $rid)), 'error');
		} else {	
		
			$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
			if (!empty($item['linkurl'])) {
				$url = $item['linkurl'];
			}else {
				$url = $_W['siteroot'] . './app/index.php?c=site&a=site&do=detail&id='.$id.'&i='.$_W['uniacid'];
			}
			$to = $this->createWebUrl('sendMobileQfMsg', array('gid' => $gid,'rid' => $rid,'id' => $id,'url' => urlencode($url)));
			header("location:$to");
			exit;
		} 
} elseif($operation == 'delete') {
	load()->func('file');
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id,rid,kid,thumb FROM ".tablename('site_article')." WHERE id = :id", array(':id' => $id));
	if (empty($row)) {
		message('抱歉，文章不存在或是已经被删除！');
	}
	if (!empty($row['thumb'])) {
		file_delete($row['thumb']);
	}
	pdo_delete('site_article', array('id' => $id));
	message('删除成功！', referer(), 'success');
} 


