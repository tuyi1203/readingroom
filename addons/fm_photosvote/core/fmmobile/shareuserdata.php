<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
		$isvisits = $_GPC['isvisits'];//是否互访
		
		$visitorsip = getip();
		
		//查询是否参与活动
		if(!empty($fromuser)) {
		    $usergift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $fromuser,':rid' => $rid));
           
            if(!empty($usergift)){
			    //添加分享人气记录
				if($fromuser!=$from_user){//自己不能给自己加人气
				    $sharedata = pdo_fetch("SELECT * FROM ".tablename($this->table_data)." WHERE fromuser = :fromuser and rid = :rid and from_user = :from_user", array(':fromuser' => $fromuser,':from_user' => $from_user,':rid' => $rid));
					if(empty($sharedata)){//一个朋友只加一次人气	
					    $insertdata = array(
		                    'uniacid'        => $uniacid,
		                    'tfrom_user'     => $tfrom_user,//分享的选手
							'fromuser'       => $fromuser,	//分享者
		                    'from_user'      => $from_user,//当前的用户
							'avatar'         => $avatar,                            
							'nickname'       => $nickname,
		                    'rid'            => $rid,
 		                    'uid'            => $usergift['uid'],
		                    'visitorsip'	 => $visitorsip,
		                    'visitorstime'   => $now
		                ); 
						pdo_insert($this->table_data, $insertdata);
						$dataid = pdo_insertid();//取id
						//给分享人添加人气量
						$sharenum = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_data)." WHERE fromuser = :fromuser and rid = :rid", array(':fromuser' => $fromuser,':rid' => $rid));
						$updatelist = array(
							'yaoqingnum' => $usergift['yaoqingnum']+1,
		                    'sharenum'  => $sharenum,
		                    'sharetime' => $now
		                );
						pdo_update($this->table_users,$updatelist,array('uid' => $usergift['uid']));	
					}
				}
				
				
				
				//转分享人页
				
				if ($_GPC['duli'] == '1') {
					$gifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuser', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));
				}elseif ($_GPC['duli'] == '2') {
					$gifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuserphotos', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));		
				}elseif ($_GPC['duli'] == '3') {
					$gifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('paihang', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));					
				}else {
					$gifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuser', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));
				}

				header("location:$gifturl");
				exit;
			}else{
				$userdata = pdo_fetch("SELECT * FROM ".tablename($this->table_data)." WHERE from_user = :from_user and tfrom_user = :tfrom_user and fromuser = :fromuser and rid = :rid", array(':from_user' => $from_user,':tfrom_user' => $tfrom_user,':fromuser' => $fromuser,':rid' => $rid));
				if (empty($userdata)) {
					$insertdata = array(
						'uniacid'        => $uniacid,
						'tfrom_user'     => $tfrom_user,//分享的选手
						'fromuser'       => $fromuser,	//分享者
						'from_user'      => $from_user,//当前的用户
						'avatar'         => $avatar,                            
						'nickname'       => $nickname,
						'rid'            => $rid,
						'uid'            => $usergift['uid'],
						'visitorsip'	 => $visitorsip,
						'visitorstime'   => $now
					); 
					pdo_insert($this->table_data, $insertdata);
				}
				
				if ($_GPC['duli'] == '1') {
					$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuser', array('rid' => $rid,'tfrom_user' => $tfrom_user,'fromuser' => $fromuser));
				}elseif ($_GPC['duli'] == '2') {
					$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuserphotos', array('rid' => $rid,'tfrom_user' => $tfrom_user,'fromuser' => $fromuser));	
				}elseif ($_GPC['duli'] == '3') {
					$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('paihang', array('rid' => $rid,'fromuser' => $fromuser,'tfrom_user' => $tfrom_user));							
				}else {
					$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvote', array('rid' => $rid));
				}
			    //转自己页
			    
				header("location:$mygifturl");
				exit;
			}
		}else{
				echo "分享人出错。一般不会出现，出现此问题，肯呢过您后台设置错误，请检查！";
			    //转自己页
				//header("location:$mygifturl");
				exit;
				//分享人出错。一般不会出现
		}		
		