<?php
/**
 * 女神来了模块定义
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
	global $_W, $_GPC;
	$cfg = $this->module['config'];
	if ($cfg['oauthtype'] != 2) {
		message('你还没有开启此功能，请在 参数设置 里开启此功能在使用！');
	}
	    $pindex = max(1, intval($_GPC['page']));

		$psize = empty($_GPC['tbrs']) ? 10 : $_GPC['tbrs'];
		$condition = '';
		$condition = "uniacid = '{$_W['uniacid']}'";
		if ($_GPC['tbfs'] == 1) {
			$condition .= " AND (`unionid` is null OR `unionid` = '')";
		}
		if ($_GPC['tbfollow'] == 1) {
			$condition .= " AND follow = '1'";
		}
		
		$totalu1 = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('mc_mapping_fans')." WHERE (`unionid` is null OR `unionid` = '') AND `uniacid` = '{$_W['uniacid']}' ");
		$totalu2 = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('mc_mapping_fans')." WHERE $condition");
		
		$totalf1 = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('mc_mapping_fans')." WHERE uniacid = '{$_W['uniacid']}' AND follow = '1'");
		$totalf2 = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('mc_mapping_fans')." WHERE uniacid = '{$_W['uniacid']}' ");
	    if (empty($_GPC['tbrs'])) {
	    	include $this->template('web/unionid');
	    	exit;
	    }
		
		$sql="SELECT * FROM ".tablename('mc_mapping_fans')." WHERE $condition ORDER BY fanid LIMIT ".($pindex - 1) * $psize.','.$psize;
		$list = pdo_fetchall($sql);
		$total= pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('mc_mapping_fans')." WHERE $condition");
		$pager = pagination($total, $pindex, $psize);	
		

		
		$access_token = WeAccount::token();
		for ($i=0; $i < count($list); $i++){
			$openid=$list[$i]['openid'];
			$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";  
			$html=file_get_contents($url); 
			$re = @json_decode($html, true);
			
			if(!empty($re['unionid'])){
				pdo_update('mc_mapping_fans',array('unionid' => $re['unionid']), array('openid'=>$openid));
			}
			//$ptotal = $pindex * $psize;
			if (($psize - 1) == $i) {
				$rtotal = ($i + 1) * $pindex;
				//$mq =  round((($pindex - 1) * $psize/$total)*100);
				$toi =($i + 1) + $_GPC['toi'];
				$mq =  round((($toi)/$total)*100);
				$msg ='';
				if (empty($_GPC['page'])) {
					
					$msg = '当前要同步的粉丝有：'.$total.' 人，由于粉丝数较多，同步会花费一些时间，请耐心等待'.'<br />';
				}
				$msg .= '正在同步中，目前：<strong style="color:#5cb85c">'.$mq.' %</strong>,当前同步人数（<strong style="color:#5cb85c">'.$rtotal.' 人</strong>）,总共粉丝数（<strong style="color:#5cb85c">'.$total.' 人</strong>）';
				
				$page = $pindex + 1;
				$to = $this->createWebUrl('getunionid', array('toi' => $toi, 'tbrs' => $_GPC['tbrs'], 'tbfs' => $_GPC['tbfs'], 'tbfollow' => $_GPC['tbfollow'], 'page' => $page));
				message($msg, $to);
			}
		}
		
		include $this->template('web/unionid');
