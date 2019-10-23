<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
		
$tfrom_user = $_GPC['tfrom_user'];
$fromuser = $_GPC['fromuser'];
//$serverapp = $_W['account']['level'];	//是否为高级号
$cfg = $this->module['config'];
$from_user = $_COOKIE["user_oauth2_openid"];
$do = $_GPC['do'];
if(!empty($fromuser)){
	if (!isset($_COOKIE["user_fromuser_openid"])) {
		setcookie("user_fromuser_openid", $fromuser, time()+3600*24*7*30);
	}
}
if(!empty($tfrom_user)){
	if (!isset($_COOKIE["user_tfrom_user_openid"])) {
		setcookie("user_tfrom_user_openid", $tfrom_user, time()+3600*24*7*30);
	}
}


if ($cfg['oauthtype'] == 3) {
	$from_user = !empty($_W['openid']) ? $_W['openid'] : 'share_no_openid';
	if(!empty($fromuser) && !isset($_COOKIE["user_fromuser_openid"])){
		setcookie("user_fromuser_openid", $fromuser, time()+3600*24*7);
	}
	if ($_GPC['duli'] == '1') {
		$photosvote = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuser', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));
	}elseif ($_GPC['duli'] == '2') {
		$photosvote = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuserphotos', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));		
	}elseif ($_GPC['duli'] == '3') {
		$photosvote = $_W['siteroot'] .'app/'.$this->createMobileUrl('paihang', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));					
	}else {
		$photosvote = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvote', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));
	}
	header("location:$photosvote");
	exit;
}else {

	if ($cfg['oauthtype'] == 1) {
		if ($do != 'shareuserdata'  && $do != 'treg'  && $do != 'tregs'  && $do != 'tvotestart'  && $do != 'tbbs'  && $do != 'tbbsreply'  && $do != 'saverecord'  && $do != 'saverecord1'  && $do != 'subscribeshare'  && $do != 'pagedata'  && $do != 'listentry'  && $do != 'code' && $do != 'reguser' && $do != 'phdata') {
			$oauthuser = $this->FM_checkoauth();	
		}			
		$from_user = empty($oauthuser['from_user']) ? $_GPC['from_user'] : $oauthuser['from_user'];
		$avatar = $oauthuser['avatar'];
		$nickname = $oauthuser['nickname'];
		$follow = $oauthuser['follow'];
	}else {
		$from_user = $_COOKIE["user_oauth2_openid"];
		if ($_W['openid'] == 'FMfromUser') {
			$from_user = $_W['openid'];
		}
		$follow = empty($follow) ? $_W['fans']['follow'] : $follow;
		$avatar = !empty($_COOKIE["user_oauth2_avatar"]) ? $_COOKIE["user_oauth2_avatar"] : $_W['fans']['tag']['avatar'];
		$nickname = !empty($_COOKIE["user_oauth2_nickname"]) ? $_COOKIE["user_oauth2_nickname"] : $_W['fans']['tag']['nickname'];
		$sex = !empty($_COOKIE["user_oauth2_sex"]) ? $_COOKIE["user_oauth2_sex"] : $_W['fans']['tag']['sex'];

		if (isset($avatar) && isset($nickname) && isset($from_user) ){

		    $shareuserdata = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserdata', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
			header("location:$shareuserdata");
			exit;
		}else{
			if ($cfg['oauthtype'] == 2) {
				$rvote = pdo_fetch("SELECT unimoshi FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
				$unionid = $_COOKIE["user_oauth2_unionid"];
				$f = pdo_fetch("SELECT follow FROM ".tablename('mc_mapping_fans') . " WHERE uniacid = $uniacid AND unionid = :unionid ", array(':unionid'=>$unionid));
				$follow = $f['follow'];
				
				if ($rvote['unimoshi'] == 1) {
					if (!empty($unionid)) {
						$user = pdo_fetch("SELECT from_user FROM ".tablename($this->table_users)." WHERE unionid = :unionid AND rid = $rid", array(':unionid'=>$unionid));
						if (!empty($user)) {
							$from_user = $user['from_user'];
						}
						
					}
				}
				if (!empty($gfrom_user)) {
					$follow = 1;
				}
				if (empty($_COOKIE["user_oauth2_openid"]) || empty($unionid)) {
					$this->checkoauth2($rid,$_COOKIE["user_oauth2_openid"],$unionid,$fromuser,$_GPC['duli']);//查询是否有cookie信息
				}
			}else {
				if (!empty($gfrom_user)) {
					$follow = 1;
				}
				if (empty($_COOKIE["user_oauth2_openid"])) {
						$this->checkoauth2($rid,$_COOKIE["user_oauth2_openid"],'',$fromuser,$_GPC['duli']);//查询是否有cookie信息
				}
			}
		}
		
	}

}
	
       
		
		
		
	