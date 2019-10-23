<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

		if ($rdisplay['ipannounce'] == 1) {
			$announce = pdo_fetchall("SELECT nickname,content,createtime,url FROM " . tablename($this->table_announce) . " WHERE rid= '{$rid}' ORDER BY id DESC");
		}
		//赞助商
		if ($rdisplay['ispaihang'] == 1) {
			$advs = pdo_fetchall("SELECT advname,link,thumb FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND rid= '{$rid}' ORDER BY displayorder ASC");
		}

		if(!empty($from_user)) {
		    $mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user,':rid' => $rid));
		}

		$tags = pdo_fetchall("SELECT * FROM ".tablename($this->table_tags)." WHERE rid = :rid ORDER BY id DESC", array(':rid' => $rid));
		$tagsarr = array();
		foreach ($tags as $key => $value) {
			$tags[$key]['piaoshu'] = pdo_fetchcolumn("SELECT sum(photosnum) FROM ".tablename($this->table_users)." WHERE tagid= ".$value['id']." AND rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnphotosnum) FROM ".tablename($this->table_users)." WHERE tagid= ".$value['id']." AND rid= ".$rid."");//累计投票
			$tags[$key]['hits'] = pdo_fetchcolumn("SELECT sum(hits) FROM ".tablename($this->table_users)." WHERE tagid= ".$value['id']." AND rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnhits) FROM ".tablename($this->table_users)." WHERE tagid= ".$value['id']." AND rid= ".$rid."");//累计投票
			//$value['title'] = $tags[$key]['piaoshu'];

			$tagsarr[$value['title']] = $tags[$key]['piaoshu'] + $tags[$key]['hits'];

		}
		$midn = 1;
		arsort($tagsarr);

		if ($_GPC['votelog'] == 1) {//投票人
			$tuser = pdo_fetch("SELECT avatar,nickname,realname,from_user FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $tfrom_user,':rid' => $rid));

			$pindex = max(1, intval($_GPC['page']));
			$psize = empty($rdisplay['phbtpxz']) ? 10 : $rdisplay['phbtpxz'];
			$m = ($pindex-1) * $psize+1;
			//取得用户列表
			$where = '';

			if (!empty($tfrom_user)) {
				$where .= " AND tfrom_user = '".$tfrom_user."'";
			}

			$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE rid = :rid '.$where.' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));
			$total = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_log).' WHERE rid = :rid '.$where.'', array(':rid' => $rid));
			$total_pages = ceil($total/$psize);
			$pager = paginationm($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
			$username = $this->getusernames($tuser['realname'], $tuser['nickname'], '6');
			$title = $username . ' 的投票用户 - ' . $rbasic['title'];
			$sharetitle = $username . '正在参加'. $rbasic['title'] .'，快来为'.$username.'投一票吧！';
			$sharecontent = $username . '正在参加'. $rbasic['title'] .'，快来为'.$username.'投一票吧！';
			$sharephoto = !empty($mygift['photo']) ? toimage($mygift['photo']) : toimage($tuser['avatar']);

			 $_share['title'] = $username . '正在参加'. $rbasic['title'] .'，快来为'.$username.'投一票吧！';
			$_share['content'] = $username . '正在参加'. $rbasic['title'] .'，快来为'.$username.'投一票吧！';
			$_share['imgUrl'] =  !empty($mygift['photo']) ? toimage($mygift['photo']) : toimage($tuser['avatar']);
		}else {//排行榜用户
			$op = $_GPC['op'];

			$tagid = !empty($_GPC['category']['childid']) ? $_GPC['category']['childid'] : $_GPC['tagid'];
			$tagpid = !empty($_GPC['category']['parentid']) ? $_GPC['category']['parentid'] : $_GPC['tagpid'];
			$tagtid = !empty($_GPC['category']['threecs']) ? $_GPC['category']['threecs'] : $_GPC['tagtid'];
			$tags = pdo_fetchall("SELECT * FROM ".tablename($this->table_tags)." WHERE rid = :rid ORDER BY id DESC", array( ':rid' => $rid));
			$tagname = $this->gettagname($tagid,$tagpid,$tagtid,$rid);
			if ($op == 'tags') {

				$pindex = max(1, intval($_GPC['page']));
				$psize =  empty($rdisplay['phbtpxz']) ? 10 : $rdisplay['phbtpxz'];
				$m = ($pindex-1) * $psize+1;
				$where = '';
				if (!empty($tagid)) {
					$where .= " AND parentid = " . $tagid;
				}elseif (!empty($tagpid)) {
					$tagids = pdo_fetchall("SELECT id FROM ".tablename($this->table_tags)." WHERE rid = :rid AND parentid = :parentid ORDER BY id DESC", array( ':rid' => $rid, ':parentid' => $tagpid));
					$where .= " AND ( ";
					foreach ($tagids as $key => $row) {
						if ($key > 0) {
							$where .= " OR parentid = " . $row['id'];
						}else{
							$where .= " parentid = " . $row['id'];
						}

					}
					$where .= " ) ";
						//print_r($where);
				}

				$tags = pdo_fetchall("SELECT id, title FROM ".tablename($this->table_tags)." WHERE rid = :rid AND icon = 3 ".$where." ORDER BY piaoshu DESC  LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));
				$total = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_tags).' WHERE rid = :rid AND icon = 3 '.$where.'', array(':rid' => $rid));
				$total_pages = ceil($total/$psize);
				/**foreach ($tags as $key => $tag) {
					$photosnum = pdo_fetchcolumn('SELECT SUM(photosnum) FROM '.tablename($this->table_users).' WHERE rid = :rid AND tagtid = :tagtid AND status = 1', array(':rid' => $rid,':tagtid' => $tag['id']));
					$xnphotosnum = pdo_fetchcolumn('SELECT SUM(xnphotosnum) FROM '.tablename($this->table_users).' WHERE rid = :rid AND tagtid = :tagtid  AND status = 1', array(':rid' => $rid,':tagtid' => $tag['id']));
					$tags[$key]['piaoshu'] .= $photosnum + $xnphotosnum;
				}
				sortArrByField($tags,'piaoshu');**/
				//print_r($tags);
				//取得用户列表


				$title = $rbasic['title'] . ' 排行榜 - ' . $_W['account']['name'];

				$_share['title'] = $rbasic['title'] . ' 排行榜 - ' . $_W['account']['name'];
				$_share['content'] = $rbasic['title'] . ' 排行榜 - ' . $_W['account']['name'];
				$_share['imgUrl'] = toimage($rshare['sharephoto']);
			}else{
				$pindex = max(1, intval($_GPC['page']));
				$psize =  empty($rdisplay['phbtpxz']) ? 10 : $rdisplay['phbtpxz'];
				$m = ($pindex-1) * $psize+1;
				//取得用户列表
				$where = '';

				$where .= " AND status = '1'";
				if (!empty($tagid)) {
					$where .= " AND tagid = '".$tagid."'";
				}
				if (!empty($tagpid)) {
					$where .= " AND tagpid = '".$tagpid."'";
				}
				if (!empty($tagtid)) {
					$where .= " AND tagtid = '".$tagtid."'";
				}

				if ($rdisplay['indexpx'] == '0') {
					$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
				}elseif ($rdisplay['indexpx'] == '1') {
					$where .= " ORDER BY `createtime` DESC";

				}elseif ($rdisplay['indexpx'] == '2') {
					$where .= " ORDER BY `hits` + `xnhits` DESC";
				}else{
					$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
				}
				$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE rid = :rid '.$where.' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));

				$total = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_users).' WHERE rid = :rid '.$where.'', array(':rid' => $rid));
				$total_pages = ceil($total/$psize);
				//$pager = paginationm($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));

				/**$userlistm = pdo_fetchall('SELECT from_user,photosnum,xnphotosnum,hits,xnhits FROM '.tablename($this->table_users).' WHERE rid = :rid '.$where.' ', array(':rid' => $rid));
				$pmsarr = array();
				foreach ($userlistm as $key => $value) {
					$pmsarr[$value['from_user']] = $value['photosnum'] + $value['xnphotosnum'];
				}
				arsort($pmsarr);
				**/
				//print_r($pmsarr);
				$title = $rbasic['title'] . ' 排行榜 - ' . $_W['account']['name'];

				$_share['title'] = $rbasic['title'] . ' 排行榜 - ' . $_W['account']['name'];
				$_share['content'] = $rbasic['title'] . ' 排行榜 - ' . $_W['account']['name'];
				$_share['imgUrl'] = toimage($rshare['sharephoto']);
			}
		}

		if (!empty($rbody)) {
			$rbody_paihang = iunserializer($rbody['rbody_paihang']);
		}

		$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli' => 3,'fromuser' => $from_user));//分享URL
		$rshare['sharetoptitle'] = str_replace('#参与人名#',$unrname,$rshare['sharetoptitle']);
		$rshare['sharetopcontent'] = str_replace('#参与人名#',$unrname,$rshare['sharetopcontent']);
		$_share['title'] = $this -> get_share($uniacid, $rid, $from_user, $rshare['sharetoptitle']);
		$_share['content'] = $this -> get_share($uniacid, $rid, $from_user, $rshare['sharetopcontent']);




		$templatename = $rbasic['templates'];
		if ($templatename != 'default' && $templatename != 'stylebase') {
			require FM_CORE. 'fmmobile/tp.php';
		}
		$toye = $this->templatec($templatename,$_GPC['do']);
		include $this->template($toye);

