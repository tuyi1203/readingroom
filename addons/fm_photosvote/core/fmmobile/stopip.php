<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

		//$reply['sharetitle']= $this->get_share($uniacid,$rid,$from_user,$reply['sharetitle']);
		//$reply['sharecontent']= $this->get_share($uniacid,$rid,$from_user,$reply['sharecontent']);
		//$myavatar = $avatar;
		//$mynickname = $nickname;
		$title = $rbasic['title'];
		if ($_GPC['iptype'] == 'local') {
			$diqu = $_GPC['diqu'];
			$nowlocal = $_GPC['nowlocal'];
			//if (!empty($nowlocal)) {
			//	setcookie("user_diqu_".$uniacid, $diqu, time()+3600*2);
			//	setcookie("user_local_".$uniacid, $nowlocal, time()+3600*2);
			//}
			if (!empty($diqu) || !empty($nowlocal) ) {
				if (empty($diqu)) {
					$diqu = '未获取到你的位置信息，请返回或者重新进入，在弹出的获取位置信息处，点击确定。';
				}
				$str = array('#限制地区#'=>$diqu,'#用户地区#'=>$nowlocal);
				$des = strtr($rvote['iplocaldes'],$str);
				
			}else {
				$des = "未获取到你的位置信息，请返回或者重新进入，在弹出的获取位置信息处，点击确定。";
			}

			
		}else {
			$des = "你所在的IP无法访问!<br/>请稍后访问";
		}
		 $_share['title'] = $this->get_share($uniacid,$rid,$from_user,$rshare['sharetitle']);
		$_share['content'] =  $this->get_share($uniacid,$rid,$from_user,$rshare['sharecontent']);
		$_share['imgUrl'] = toimage($rshare['sharephoto']);
		
		
		$toye = $this->_stopllq('stopip');
		include $this->template($toye);
		