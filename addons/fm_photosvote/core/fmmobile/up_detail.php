<?php
/**
 * 选手详情
 *
 * @author 未梦
 * @url http://www.weimengcms.com/
 */
defined('IN_IA') or exit('Access Denied');

		$regtitlearr = iunserializer($rdisplay['regtitlearr']);
		$vfrom = $_GPC['do'];
		if ($rvote['votepay']==1) {
			if ($_W['account']['level'] == 4) {
				$u_uniacid = $uniacid;
			}else{
				$u_uniacid = $cfg['u_uniacid'];
			}
			$pays = pdo_fetch("SELECT payment FROM " . tablename('uni_settings') . " WHERE uniacid='{$u_uniacid}' limit 1");
			$pay = iunserializer($pays['payment']);


			if (!empty($_GPC['paymore'])) {
				$paymore = iunserializer(base64_decode(base64_decode($_GPC['paymore'])));
				//print_r($paymore);
			}
			$payordersn = pdo_fetch("SELECT id,payyz,ordersn FROM " . tablename($this->table_order) . " WHERE rid='{$rid}' AND from_user = :from_user AND paytype = 2 ORDER BY id DESC,paytime DESC limit 1", array(':from_user'=>$from_user));
			$voteordersn = pdo_fetch("SELECT id FROM " . tablename($this->table_log) . " WHERE rid='{$rid}' AND from_user = :from_user AND ordersn = :ordersn  AND tptype =3 ORDER BY id DESC limit 1", array(':from_user'=>$from_user,':ordersn'=>$paymore['ordersn']));

		}
		if ($rdisplay['ipannounce'] == 1) {
			$announce = pdo_fetchall("SELECT nickname,content,createtime,url FROM " . tablename($this->table_announce) . " WHERE rid= '{$rid}' ORDER BY id DESC");
		}
		
		//查询自己是否参与活动
		if(!empty($from_user)) {
		    $mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user,':rid' => $rid));
		    $voteer = pdo_fetch("SELECT realname,mobile FROM ".tablename($this->table_voteer)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user,':rid' => $rid));
		}

		//查询是否参与活动
		if(!empty($tfrom_user)) {
		    $user = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $tfrom_user,':rid' => $rid));
		  	if ($user['status'] != 1 && $tfrom_user != $from_user) {
				$urlstatus =  $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvote',array('rid'=> $rid));
				echo "<script>alert('ID:".$user['uid']." 号选手正在审核中，请查看其他选手，谢谢！');location.href='".$urlstatus."';</script>";
				die();
		  		//message('该选手正在审核中，请查看其他选手，谢谢！',$this->createMobileUrl('photosvote',array('rid'=> $rid)),'error');
		  	}
			if ($user['ewm'] && $user['haibao'] && file_exists($user['haibao'])) {
				$ewmurl = tomedia($user['haibao']).'?v=' . $rid;
			}else{
				$url_send = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli'=> '1', 'fromuser' => $from_user, 'tfrom_user' => $tfrom_user));

				$url_jieguo = $this->wxdwz($url_send);
				$url = $url_jieguo['short_url'];

				$qrcode = array(
					//'expire_seconds' => '2592000',
					'action_name' => 'QR_LIMIT_SCENE',//QR_LIMIT_SCENE  QR_LIMIT_STR_SCENE
					'action_info' => array(
									'scene'	=> array(
												'scene_id' => cutstr($rid, '1') . $user['uid'],
												//'scene_str' => 't998'
											),
									),
				);
				$qrcodearr = base64_encode(iserializer($qrcode));
			}

		  	if ($user) {
		  		$paihangcha = $this->GetPaihangcha($rid, $tfrom_user, $rdisplay['indexpx']);
				$yuedu = $from_user.$rid.$uniacid;
				if (time() == mktime(0,0,0)) {
					setcookie("user_yuedu", -10000);
				}
//
				if ($_COOKIE["user_yuedu"] != $yuedu) {
					 pdo_update($this->table_users, array('hits' => $user['hits']+1), array('rid' => $rid, 'from_user' => $tfrom_user));
					 setcookie("user_yuedu", $yuedu, time()+3600*24);
				}
				//print_r($tfrom_user);
		    }else{
				$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvote', array('rid' => $rid));
				header("location:$url");
				exit;
			}
			$tagname = $this->gettagname($user['tagid'],$user['tagpid'],$user['tagtid'],$rid);
		}
		$sharenum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE tfrom_user = :tfrom_user and rid = :rid", array(':tfrom_user' => $tfrom_user,':rid' => $rid)) + pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE fromuser = :fromuser and rid = :rid", array(':fromuser' => $tfrom_user,':rid' => $rid)) + $user['sharenum'];

		//$picarr = $this->getpicarr($uniacid,$reply['tpxz'],$tfrom_user,$rid);
		$fmimage = $this->getpicarr($uniacid,$rid, $tfrom_user,1);
		$picarrs =  pdo_fetchall("SELECT id, photos,from_user,isfm FROM ".tablename($this->table_users_picarr)." WHERE from_user = :from_user AND rid = :rid ORDER BY isfm DESC", array(':from_user' => $user['from_user'],':rid' => $rid));
		$level = $this->fmvipleavel($rid, $uniacid, $user['from_user']);
		$rjifen = pdo_fetch("SELECT is_open_jifen,is_open_jifen_sync,jifen_vote,jifen_vote_reg,jifen_reg FROM ".tablename($this->table_jifen)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$zsgift = pdo_fetchall("SELECT giftid, SUM(giftnum) as num FROM " . tablename($this->table_user_zsgift) . ' WHERE rid = :rid AND tfrom_user = :tfrom_user ' . $uni . ' GROUP BY giftid ASC', array(':rid' => $rid, ':tfrom_user' => $tfrom_user));
		foreach ($zsgift as $key => $value) {
			$g = pdo_fetch("SELECT * FROM " . tablename($this -> table_jifen_gift) . ' WHERE id = :id ' . $uni . '', array(':id' => $value['giftid']));
			$zsgift[$key]['title'] = cutstr($g['gifttitle'], '4');
			$zsgift[$key]['images'] = tomedia($g['images']);
		}
		$total_gift = count($zsgift);

		if($rbasic['isdaojishi']==1) {
			$starttime=mktime(0,0,0);//当天：00：00：00
			$endtime = mktime(23,59,59);//当天：23：59：59
			$times = '';
			$times .= ' AND createtime >=' .$starttime;
			$times .= ' AND createtime <=' .$endtime;

			$uservote = pdo_fetch("SELECT * FROM ".tablename($this->table_log)." WHERE from_user = :from_user  AND tfrom_user = :tfrom_user AND rid = :rid", array(':from_user' => $from_user,':tfrom_user' => $tfrom_user,':rid' => $rid));
			$uallonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid  ORDER BY createtime DESC', array(':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));

			$udayonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));

		}

		if ($rdisplay['isvoteusers']) {
			$voteuserlist = pdo_fetchall('SELECT avatar,nickname FROM '.tablename($this->table_log).' WHERE rid = :rid  AND tfrom_user = :tfrom_user GROUP BY `nickname` ORDER BY `id` DESC LIMIT 5', array(':rid' => $rid,':tfrom_user' => $tfrom_user));
		}

		
		if ($rbasic['isdaojishi']) {
			$votetime = $rbasic['votetime']*3600*24;
			$isvtime = TIMESTAMP - $user['createtime'];
			$ttime = $votetime - $isvtime;

			if ($ttime > 0) {
				$totaltime = $ttime;
			} else {
				$totaltime = 0;
			}
		}

		$now = time();
		if($now-$rdisplay['xuninum_time']>$rdisplay['xuninumtime']){
		    pdo_update($this->table_reply_display, array('xuninum_time' => $now,'xuninum' => $rdisplay['xuninum']+mt_rand($rdisplay['xuninuminitial'],$rdisplay['xuninumending'])), array('rid' => $rid));
		}
	
		if (!empty($rbody)) {
			$rbody_tuser = iunserializer($rbody['rbody_tuser']);
		}


    	echo json_encode($user);
		exit;

