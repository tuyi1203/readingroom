<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

//查询活动是否开启了抽奖
$upload = pdo_fetch("SELECT * FROM " . tablename('fm_photosvote_wm_upload') . " WHERE rid='{$rid}' limit 1");
$rvote['islottery'] = $upload['islottery'];

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
		if ($rdisplay['isvotexq'] == 1) {
			$advs = pdo_fetchall("SELECT advname,link,thumb FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND rid= '{$rid}' AND issuiji = 1");

			if (!empty($advs)) {
				$adv  = array_rand($advs);
				$advarr = array();
				$advarr['thumb'] .= toimage($advs[$adv]['thumb']);
				$advarr['advname'] .= cutstr($advs[$adv]['advname'], '10');
				$advarr['link'] .= $advs[$adv]['link'];
			}
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

		    if ($user) {
				$yuedu = $tfrom_user.$from_user.$rid.$uniacid;
				//setcookie("user_yuedu", -10000);
			   if ($_COOKIE["user_yuedup"] != $yuedu) {
					 pdo_update($this->table_users, array('hits' => $user['hits']+1,), array('rid' => $rid, 'from_user' => $tfrom_user));
					 setcookie("user_yuedup", $yuedu, time()+3600*24);
				}
		    }
		}

		$picarrs =  pdo_fetchall("SELECT id, photos,from_user FROM ".tablename($this->table_users_picarr)." WHERE from_user = :from_user AND rid = :rid ORDER BY isfm DESC ", array(':from_user' => $user['from_user'],':rid' => $rid));

		$starttime=mktime(0,0,0);//当天：00：00：00
		$endtime = mktime(23,59,59);//当天：23：59：59
		$times = '';
		$times .= ' AND createtime >=' .$starttime;
		$times .= ' AND createtime <=' .$endtime;
		$uservote = pdo_fetch("SELECT * FROM ".tablename($this->table_log)." WHERE from_user = :from_user  AND tfrom_user = :tfrom_user AND rid = :rid", array(':from_user' => $from_user,':tfrom_user' => $tfrom_user,':rid' => $rid));
		$uallonetp = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_log).' WHERE from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid ORDER BY createtime DESC', array(':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
		$udayonetp = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_log).' WHERE from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
		$unrname = !empty($user['realname']) ? $user['realname'] : $user['nickname'] ;

		$title = $unrname . '正在参加'. $rbasic['title'] .'，快来为'.$unrname.'投票吧！';

		$fmimage = $this->getpicarr($uniacid,$rid, $tfrom_user,1);


		$_share['link'] =$_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli'=> '2', 'fromuser' => $from_user, 'tfrom_user' => $tfrom_user));//分享URL
		//$_share['title'] = $unrname . '正在参加'. $rbasic['title'] .'，快来为'.$unrname.'投一票吧！';
		//$_share['content'] = $unrname . '正在参加'. $rbasic['title'] .'，快来为'.$unrname.'投一票吧！';
		$_share['imgUrl'] =  $this->getphotos($fmimage['photos'],$user['avatar'],  $rbasic['picture']);
		//分享URL
		$rshare['shareusertitle'] = str_replace('#参与人名#',$unrname,$rshare['shareusertitle']);
		$rshare['shareusercontent'] = str_replace('#参与人名#',$unrname,$rshare['shareusercontent']);
		$_share['title'] = $this -> get_share($uniacid, $rid, $from_user, $rshare['shareusertitle']);
		$_share['content'] = $this -> get_share($uniacid, $rid, $from_user, $rshare['shareusercontent']);



		$templatename = $rbasic['templates'];
		if ($templatename != 'default' && $templatename != 'stylebase') {
			require FM_CORE. 'fmmobile/tp.php';
		}
		$toye = $this->templatec($templatename,$_GPC['do']);
		include $this->template($toye);
