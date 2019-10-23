<?php
/**
 * [Fmoons System] Copyright (c) 2016 FMOONS.COM
 * Fmoons isNOT a free software, it under the license terms, visited http://www.fmoons.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$tempdo = empty($_GPC['tempdo']) ? "" : $_GPC['tempdo'];
$pageid = empty($_GPC['pageid']) ? "" : $_GPC['pageid'];
$menuid = empty($_GPC['menuid']) ? "" : $_GPC['menuid'];
$apido = empty($_GPC['apido']) ? "" : $_GPC['apido'];

$rid = intval($_GPC['rid']);
$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
if($operation == 'display') {
	$rid = intval($_GPC['rid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	
	$templates = pdo_fetchall("SELECT * FROM ".tablename($this->table_templates)." WHERE uniacid = '{$_W['uniacid']}' or uniacid = 0 ORDER BY name ASC, createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_templates) . " WHERE uniacid = '{$_W['uniacid']}' or uniacid = 0");
	$pager = pagination($total, $pindex, $psize);
	
	include $this->template('web/templates');
} elseif($operation == 'post') {
	load()->func('tpl');
	$id = intval($_GPC['id']);
	$rid = intval($_GPC['rid']);
	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM ".tablename($this->table_templates)." WHERE id = :id" , array(':id' => $id));
	} 
	$files = array('1' =>'photosvote.html', '2'=>'tuser.html', '3'=>'paihang.html', '4' =>'reg.html', '5'=>'des.html');
	if (checksubmit('submit')) {
		
		define('REGULAR_STYLENAME', '/^(([a-z]+[0-9]+)|([a-z]+))[a-z0-9]*$/i');
		
		if(!preg_match(REGULAR_STYLENAME, $_GPC['stylename'])) {
			message('必须输入模板标识，格式为 字母（不区分大小写）+ 数字,不能出现中文、中文字符');
		}
		if (empty($_GPC['title'])) {
			message('标题不能为空，请输入标题！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'version' => $_GPC['version'],
			'description' => $_GPC['description'],
			'author' => $_GPC['author'],
			'thumb' => $_GPC['thumb'],
			'url' => $_GPC['url'],
			'type' => 'all',
			'createtime' => TIMESTAMP
		);
		if (empty($id)) {
			if ($_GPC['stylename'] == $item['templates']) {
				message('该模板标识已存在，请更换');
			}
			$data['name'] = $_GPC['stylename'];
			pdo_insert($this->table_templates, $data);
			$aid = pdo_insertid();
		} else {
			$data['name'] = $item['name'];
			unset($data['createtime']);
			pdo_update($this->table_templates, $data, array('id' => $id));
		}
		message('模板更新成功！', $this->createWebUrl('templates', array('op' => 'display', 'rid' => $rid)), 'success');
	}
	include $this->template('web/templates');
} elseif($operation == 'designer') {

	$stylename = $_GPC['stylename'];
	$pagetype = $_GPC['pagetype'];
	$pages = pdo_fetchall("SELECT id,pagename,pagetype,setdefault FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid  ", array(':uniacid' => $_W['uniacid']));
	$menus = pdo_fetchall('select * from ' . tablename($this->table_designer_menu) . ' where uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
	$reply = pdo_fetchall("SELECT title,rid FROM " . tablename($this->table_reply) . " WHERE status=:status and uniacid= :uniacid  ", array(':uniacid' => $_W['uniacid'], ':status' => '1'));
	$allusers = pdo_fetchall("SELECT id,rid,from_user,nickname,realname,uid,avatar,photosnum,hits,xnphotosnum,xnhits,sharenum FROM " . tablename($this->table_users) . " WHERE uniacid= :uniacid AND status=:status ORDER BY uid ASC", array(':uniacid' => $_W['uniacid'], ':status' => '1'));
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	
	$templates = pdo_fetchall("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = '{$_W['uniacid']}' AND status = 1 ORDER BY uid ASC, createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_users) . " WHERE uniacid = '{$_W['uniacid']}' AND status = 1");
	$pager = pagination($total, $pindex, $psize);
	
	if (!empty($pageid)) {
		$datas = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $pageid));
	}else{
		$datas = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid AND stylename =:stylename AND pagetype=:pagetype ", array(':uniacid' => $_W['uniacid'], ':stylename' => $stylename, ':pagetype' => $pagetype));
	}
	$pageid = empty($_GPC['pageid']) ? $datas['id'] : $_GPC['pageid'];
	$pagetype = empty($_GPC['pagetype']) ? $datas['pagetype'] : $_GPC['pagetype'];
	if (!empty($datas)) {
		
		$data = htmlspecialchars_decode($datas['datas']);
		$d = json_decode($data, true);
		$usersids = array();
		foreach ($d as $k1 => &$dd) {
			if ($dd['temp'] == 'photosvote') {
				foreach ($dd['data'] as $k2 => $ddd) {
					$usersids[] = array('id' => $ddd['usersid'], 'k1' => $k1, 'k2' => $k2);
				} 
			} elseif ($dd['temp'] == 'richtext') {
				$dd['content'] = $this -> unescape($dd['content']);
			} 
		} 
		unset($dd);
		$arr = array();
		foreach($usersids as $a) {
			$arr[] = $a['id'];
		} 
		if (count($arr) > 0) {
			$usersinfo = pdo_fetchall("SELECT id,rid,from_user,nickname,realname,uid,avatar,photosnum,hits,xnphotosnum,xnhits,sharenum FROM " . tablename($this->table_users) . " WHERE id in ( " . implode(',', $arr) . ") AND uniacid= :uniacid AND status=:status AND rid =:rid ORDER BY uid ASC", array(':uniacid' => $_W['uniacid'], ':status' => '1', ':rid' => 34), 'id');
			
			$usersinfo = $this->set_medias($usersinfo, 'avatar');
			foreach ($d as $k1 => &$dd) {
				if ($dd['temp'] == 'photosvote') {
					foreach ($dd['data'] as $k2 => &$ddd) {
						$cdata = $usersinfo[$ddd['usersid']];
						$ddd['name'] = !empty($cdata['nickname']) ? $cdata['nickname'] : $cdata['realname'] ;
						$ddd['uid'] = $cdata['uid'];
						$ddd['from_user'] = $cdata['from_user'];
						$ddd['piaoshu'] = $cdata['photosnum'] + $cdata['xnphotosnum'];
						$ddd['img'] = $cdata['avatar'];
						$ddd['renqi'] = $cdata['hits'] + $cdata['xnhits'];
						$ddd['sharenum'] = $cdata['sharenum'];
					} 
					unset($ddd);
				} 
			} 
			unset($dd);
		}
		$data = json_encode($d);
		$data = rtrim($data, "]");
		$data = ltrim($data, "[");
		$pageinfo = htmlspecialchars_decode($datas['pageinfo']);
		$pageinfo = rtrim($pageinfo, "]");
		$pageinfo = ltrim($pageinfo, "[");
		$users = $this->getMember($from_user);
		$usersname = !empty($users['realname']) ? $users['realname'] : $users['nickname'] ;
		$system = array('tusertop' => array('name' => $usersname, 'logo' => tomedia($users['avatar'])));
		$system = json_encode($system);
	} else {
		$pageinfo = "{id:'M0000000000000',temp:'topbar',params:{title:'',desc:'',img:'',kw:'',footer:'1',floatico:'0',floatstyle:'right',floatwidth:'40px',floattop:'100px',floatimg:'',floatlink:''}}";
	}
	include $this->template('web/templates');
} elseif($operation == 'default') {
	$rid = intval($_GPC['rid']);
	
	if (!empty($rid) && !empty($_GPC['templatesname']) && $rid <> 0) {
		pdo_update($this->table_reply, array('templates' => $_GPC['templatesname']), array('rid' => $rid));
		$fmdata = array(
			"success" => 1,
			"msg" => '设置默认模板成功！'
		);
		echo json_encode($fmdata);
		exit();	
	}else{
		$fmdata = array(
			"success" => -1,
			"msg" => '设置默认模板错误！'
		);
		echo json_encode($fmdata);
		exit();
	}
	
} elseif($operation == 'delete') {
	load()->func('file');
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id,thumb,stylename FROM ".tablename($this->table_templates)." WHERE id = :id", array(':id' => $id));
	if (empty($row)) {
		message('抱歉，模板不存在或是已经被删除！');
	}
	if (!empty($row['thumb'])) {
		file_delete($row['thumb']);
	}
	pdo_delete($this->table_templates, array('id' => $id));
	pdo_delete($this->table_designer, array('stylename' => $row['stylename']));
	message('删除成功！', $this->createWebUrl('templates', array('op' => 'display', 'rid' => $rid)), 'success');
} elseif ($operation == 'api') {
	if ($_W['ispost']) {
		if ($apido == 'savepage') {
			$id = $_GPC['pageid'];
			$datas = json_decode(htmlspecialchars_decode($_GPC['datas']), true);
			$date = date("Y-m-d H:i:s");
			$pagename = $_GPC['pagename'];
			$pagetype = $_GPC['pagetype'];
			$stylename = $_GPC['stylename'];
			$pageinfo = $_GPC['pageinfo'];
			$stylename = $_GPC['stylename'];

			$p = htmlspecialchars_decode($pageinfo);
			$p = json_decode($p, true);
			$keyword = empty($p[0]['params']['kw']) ? "" : $p[0]['params']['kw'];
			$p[0]['params']['img'] = $this->save_media($p[0]['params']['img']);
			foreach ($datas as &$data) {
				if ($data['temp'] == 'banner' || $data['temp'] == 'menu' || $data['temp'] == 'picture') {
					foreach ($data['data'] as &$d) {
						$d['imgurl'] = $this->save_media($d['imgurl']);
					}
					unset($d);
				} else if ($data['temp'] == 'pusers') {
					$data['params']['bgimg'] = $this->save_media($data['params']['bgimg']);
				} else if ($data['temp'] == 'photosvote') {
					foreach ($data['data'] as &$d) {
						$d['img'] = $this->save_media($d['img']);
					}
					unset($d);
				} else if ($data['temp'] == 'richtext') {
					//$content = m('common')->html_images($this->unescape($data['content']));
					///$data['content'] = $this->escape($content);
				} else if ($data['temp'] == 'cube') {
					foreach ($data['params']['layout'] as &$row) {
						foreach ($row as &$col) {
							$col['imgurl'] = $this->save_media($col['imgurl']);
						}
						unset($col);
					}
					unset($row);
				}
			}
			unset($data);

			$insert = array('pagename' => $pagename, 'pagetype' => $pagetype, 'stylename' => $stylename, 'pageinfo' => json_encode($p), 'savetime' => $date, 'datas' => json_encode($datas), 'uniacid' => $_W['uniacid'], 'keyword' => $keyword,);
			if (empty($id)) {
				$insert['createtime'] = $date;
				pdo_insert($this->table_designer, $insert);
				$id = pdo_insertid();
				load()->func('file');
				
				$file = gettemplates($pagetype);
		
				$targetfile = IA_ROOT . '/addons/fm_photosvote/template/mobile/templates/' . $stylename . '/' . $file;
				//if(!file_exists($targetfile)) {
					mkdirs(dirname($targetfile));
					$content = file_get_contents(IA_ROOT . "/addons/fm_photosvote/template/addons/designer/mobile/default/index.html");
					file_put_contents($targetfile, $content);
					@chmod($targetfile, $_W['config']['setting']['filemode']);
				//}
			
			} else {
				load()->func('file');
				
				$file = gettemplates($pagetype);
				
				
				$targetfile = IA_ROOT . '/addons/fm_photosvote/template/mobile/templates/' . $stylename . '/' . $file;
				//if(!file_exists($targetfile)) {
					mkdirs(dirname($targetfile));
					//$content = getcontent();
					$content = file_get_contents(IA_ROOT . "/addons/fm_photosvote/template/addons/designer/mobile/default/index.html");
					file_delete($targetfile);
					file_put_contents($targetfile, $content);
					@chmod($targetfile, $_W['config']['setting']['filemode']);
				//}
			
				if ($pagetype == '4') {
					$insert['setdefault'] = '0';
				}
				pdo_update($this->table_designer, $insert, array('id' => $id));
			}
			echo $id;
			exit;
		} elseif ($apido == 'delpage') {
			if (empty($pageid)) {
				message('删除失败！Url参数错误', $this->createWebUrl('templates'), 'error');
			} else {
				$page = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $pageid));
				if (empty($page)) {
					echo '删除失败！目标页面不存在！';
					exit();
				} else {
					$do = pdo_delete($this->table_designer, array('id' => $pageid));
					if ($do) {
						echo 'success';
					} else {
						echo '删除失败！';
					}
				}
			}
		} elseif ($apido == 'selectusers') {
			$kw = $_GPC['kw'];
			$rid = intval($_GPC['rid']);
			//$where ="";
			//if (!empty($kw)) {
			//	$where .= " AND (nickname LIKE %{$kw}% OR realname LIKE %{$kw}%) ";
			//}
			$users = pdo_fetchall("SELECT id,rid,from_user,nickname,realname,uid,avatar,photosnum,hits,xnphotosnum,xnhits,sharenum FROM " . tablename($this->table_users) . " WHERE uniacid= :uniacid AND status=:status AND rid =:rid AND nickname LIKE :nickname ORDER BY uid ASC", array(':uniacid' => $_W['uniacid'], ':status' => '1', ':rid' => $rid, ':nickname' => "%{$kw}%", ));
			foreach ($users as $key => $value) {
				$fmimage = $this->getpicarr($_W['uniacid'],$rid, $value['from_user'],1);
				$fengmian = $this->getphotos($value['avatar'], $fmimage['photos'], $value['picture']);
				$users[$key]['mphotos'] = $fengmian;
				$users[$key]['piaoshu'] = $value['photosnum'] +  $value['xnphotosnum'];
				$users[$key]['renqi'] = $value['hits'] +  $value['xnhits'];
				$users[$key]['name'] = empty($value['realname']) ? $value['nickname'] : $value['realname'] ;
			}

			$users = $this->set_medias($users, array('avatar','mphotos'));
			echo json_encode($users);
		} elseif ($apido == 'setdefault') {
			$do = $_GPC['d'];
			$id = $_GPC['id'];
			$type = $_GPC['type'];
			if ($do == 'on') {
				$pages = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE pagetype=:pagetype AND setdefault=:setdefault AND uniacid=:uniacid ", array(':pagetype' => $type, ':setdefault' => '1', ':uniacid' => $_W['uniacid']));
				if (!empty($pages)) {
					$array = array('setdefault' => '0');
					pdo_update($this->table_designer, $array, array('id' => $pages['id']));
				}
				$array = array('setdefault' => '1');
				$action = pdo_update($this->table_designer, $array, array('id' => $id));
				if ($action) {
					$json = array('result' => 'on', 'id' => $id, 'closeid' => $pages['id']);
					echo json_encode($json);
				}
			} else {
				$pages = pdo_fetch("SELECT * FROM " . tablename($this->table_designer) . " WHERE  id=:id and uniacid=:uniacid ", array(':id' => $id, ':uniacid' => $_W['uniacid']));
				if ($pages['setdefault'] == 1) {
					$array = array('setdefault' => '0');
					$action = pdo_update($this->table_designer, $array, array('id' => $pages['id']));
					if ($action) {
						$json = array('result' => 'off', 'id' => $pages['id']);
						echo json_encode($json);
					}
				}
			}
		} elseif ($apido == 'selectkeyword') {
			$kw = $_GPC['kw'];
			$rid = $_GPC['rid'];
			$pid = $_GPC['pid'];
			$rule = pdo_fetch("select * from " . tablename('rule_keyword') . ' where content=:content and uniacid=:uniacid and module=:module limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'ewei_shop', ':content' => $kw));
			if (empty($rule)) {
				echo 'ok';
			} else {
				$rule2 = pdo_fetch("select * from " . tablename('rule') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $rule['rid']));
				if ($rule2['name'] == 'ewei_shop:designer:' . $pid) {
					echo 'ok';
				}
			}
		} elseif ($apido == 'selectlink') {
			$type = $_GPC['type'];
			$kw = $_GPC['kw'];
			$rid = $_GPC['rid'];
			if ($type == 'notice') {
				$notices = pdo_fetchall("select * from " . tablename('ewei_shop_notice') . ' where title LIKE :title and status=:status and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':status' => '1', ':title' => "%{$kw}%"));
				echo json_encode($notices);
			} elseif ($type == 'users') {
				$where ='';
				if (!empty($kw)) {
					$where .= ' AND (nickname LIKE %{$kw}% OR realname LIKE %{$kw}%) ';
				}
				$users = pdo_fetchall("SELECT id,rid,from_user,nickname,realname,uid,avatar,photosnum,hits,xnphotosnum,xnhits,sharenum FROM " . tablename($this->table_users) . " WHERE uniacid= :uniacid AND status=:status AND rid =:rid $where ORDER BY uid ASC", array(':uniacid' => $_W['uniacid'], ':status' => '1', ':rid' => $rid));
				echo json_encode($users);
			} else {
				exit();
			}
		}
	}
	exit();
}elseif ($operation == 'menu') {
	$reply = pdo_fetchall("SELECT title,rid FROM " . tablename($this->table_reply) . " WHERE status=1 and uniacid= :uniacid  ", array(':uniacid' => $_W['uniacid']));
	$foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';
	if ($foo == 'display') {
		$page = empty($_GPC['page']) ? "" : $_GPC['page'];
		$pindex = max(1, intval($page));
		$psize = 10;
		$kw = empty($_GPC['keyword']) ? "" : $_GPC['keyword'];
		$menus = pdo_fetchall("SELECT * FROM " . tablename($this->table_designer_menu) . " WHERE uniacid= :uniacid and menuname LIKE :name " . "ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':name' => "%{$kw}%"));
		$menusnum = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_designer_menu) . " WHERE uniacid= :uniacid " . "ORDER BY createtime DESC ", array(':uniacid' => $_W['uniacid']));
		$total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_designer_menu) . " WHERE uniacid= :uniacid and menuname LIKE :name " . "ORDER BY createtime DESC ", array(':uniacid' => $_W['uniacid'], ':name' => "%{$kw}%"));
		$pager = pagination($total, $pindex, $psize);
	} elseif ($foo == 'post') {
		$reply = pdo_fetchall("SELECT title,rid FROM " . tablename($this->table_reply) . " WHERE status=:status and uniacid= :uniacid  ", array(':uniacid' => $_W['uniacid'], ':status' => '1'));
		$menu = pdo_fetch('select * from ' . tablename($this->table_designer_menu) . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $menuid, ':uniacid' => $_W['uniacid']));
		$params = array("previewbg" => '#999999', "height" => '49px', "textcolor" => '#666666', "textcolorhigh" => '#666666', "iconcolor" => '#666666', "iconcolorhigh" => '#666666', "bgcolor" => '#fafafa', "bgcolorhigh" => '#fafafa', "bordercolor" => '#bfbfbf', "bordercolorhigh" => '#bfbfbf', "showtext" => 1, "showborder" => 1, "showicon" => 1, "textcolor2" => '#666666', "bgcolor2" => '#fafafa', "bordercolor2" => '#bfbfbf', "showborder2" => 1);
		$menus = array(array("id" => 1, "title" => '女神来了', "icon" => 'fa fa-list', "url" => '', "subMenus" => array(array('title' => '测试', 'url' => 'www.baidu.com'))));
		if (!empty($menu)) {
			$menus = json_decode($menu['menus'], true);
			$params = json_decode($menu['params'], true);
		}
		foreach ($menus as $key => &$m) {
			$m['textcolor'] = empty($key) ? $params['textcolorhigh'] : $params['textcolor'];
			$m['bgcolor'] = empty($key) ? $params['bgcolorhigh'] : $params['bgcolor'];
			$m['bordercolor'] = empty($key) ? $params['bordercolorhigh'] : $params['bordercolor'];
			$m['iconcolor'] = empty($key) ? $params['iconcolorhigh'] : $params['iconcolor'];
		}
		unset($m);
		$pages = pdo_fetchall("SELECT id,pagename,pagetype,setdefault FROM " . tablename($this->table_designer) . " WHERE uniacid= :uniacid  ", array(':uniacid' => $_W['uniacid']));
		if ($_W['ispost'] && $_W['isajax']) {
			$menus = htmlspecialchars_decode($_GPC['menus']);
			$params = htmlspecialchars_decode($_GPC['params']);
			if (empty($menus) || empty($params)) {
				die(json_encode(array('result' => 0, 'message' => '参数错误!')));
			}
			$data = array('uniacid' => $_W['uniacid'], 'menuname' => $_GPC['menuname'], 'params' => $params, 'menus' => $menus,);
			if (empty($menu)) {
				$data['createtime'] = time();
				pdo_insert($this->table_designer_menu, $data);
				$menuid = pdo_insertid();
			} else {
				pdo_update($this->table_designer_menu, $data, array('id' => $menuid, 'uniacid' => $_W['uniacid']));
			}
			die(json_encode(array('result' => 1, 'menuid' => $menuid)));
		}
	} elseif ($foo == 'delete') {
		if (empty($menuid)) {
			die('参数错误!');
		}
		$menu = pdo_fetch("SELECT * FROM " . tablename($this->table_designer_menu) . " WHERE uniacid= :uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $menuid));
		if (empty($menu)) {
			die('菜单未找到!');
		}
		pdo_delete($this->table_designer_menu, array('id' => $menuid, 'uniacid' => $_W['uniacid']));
		die('success');
	} elseif ($foo == 'setdefault') {
		if (empty($menuid)) {
			die('参数错误!');
		}
		$menu = pdo_fetch("SELECT * FROM " . tablename($this->table_designer_menu) . " WHERE uniacid= :uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $menuid));
		if (empty($menu)) {
			die('菜单未找到!');
		}
		if ($_GPC['d'] == 'on') {
			pdo_update($this->table_designer_menu, array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
			pdo_update($this->table_designer_menu, array('isdefault' => 1), array('id' => $menuid, 'uniacid' => $_W['uniacid']));
		} else {
			pdo_update($this->table_designer_menu, array('isdefault' => 0), array('id' => $menuid, 'uniacid' => $_W['uniacid']));
		}
		die('success');
	}
	include $this->template('web/templates');
}

function gettemplates($pagetype) {
	
	switch ($pagetype) {
	  case '1':
	    $name = 'photosvote.html';
	    break;
	  case '2':
	    $name = 'tuser.html';
	    break;
	  case '3':
	    $name = 'paihang.html';
	    break;
	  case '4':
	    $name = 'reg.html';
	    break;
	  case '5':
	    $name = 'des.html';
	    break;
	  
	  default:
	    $name = 'photosvote.html';
	    break;
	}
	return $name;
}
function getnames($names) {
	switch ($names) {
	  case 'photosvote.html':
	    $name = '投票首页';
	    break;
	  case 'tuser.html':
	    $name = '投票详情页';
	    break;
	  case 'tuserphotos.html':
	    $name = '投票相册展示页';
	    break;
	  case 'reg.html':
	    $name = '注册报名页';
	    break;
	  case 'paihang.html':
	    $name = '排行榜页';
	    break;
	  case 'des.html':
	    $name = '活动详情页';
	    break;
	  
	  default:
	    $name = '女神来了';
	    break;
	}
	return $name;
}
