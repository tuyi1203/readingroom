<?php
/**
 * 小智-微直播（传播版）模块微站定义
 *
 * @author wxz
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
define('TIMESTAMP',time());
define('ROOT_PATH', str_replace('site.php', '', str_replace('\\', '/', __FILE__)));
require_once ROOT_PATH."getip/IP.class.php";

class Wxz_wzbModuleSite extends WeModuleSite {

	public function doMobileIndex(){
		global $_W,$_GPC;
		$sub_openid = $_GPC['sub_openid'];
		$share_uid = $_GPC['share_uid'];
		$uid = $this->auths();

		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		if (!$user['sub_openid']){
			if($sub_openid ){
				$data=array(
					'sub_openid'=>$sub_openid
				);
				pdo_update('wxz_wzb_user', $data, array('id' => $uid));
			}
		}

		$item = pdo_fetchAll('SELECT a.*,b.logo as img FROM ' . tablename('wxz_wzb_live_setting').' as a left join ' . tablename('wxz_wzb_setting').' as b on a.rid = b.rid where a.`uniacid` = '.$_W['uniacid'].' order by a.id desc');
		$live = pdo_fetchAll('SELECT a.*,b.logo as img FROM ' . tablename('wxz_wzb_live_setting').' as a left join ' . tablename('wxz_wzb_setting').' as b on a.rid = b.rid where a.`uniacid` = '.$_W['uniacid'].'  AND a.isshow=1  order by total_num desc limit 3');
		$lists = array();
		$list = pdo_fetchall("SELECT * FROM ".tablename('wxz_wzb_category')." WHERE uniacid=:uniacid AND isshow=:isshow  ORDER BY sort ASC,dateline DESC",array(':uniacid'=>$_W['uniacid'],':isshow'=>'1'));
		if(!empty($list) && is_array($list)){
			foreach($list as $key=>$row){
					$row['list'] = pdo_fetchall("SELECT * FROM ".tablename('wxz_wzb_live_setting')." WHERE uniacid=:uniacid AND cid=:cid AND isshow=:isshow ORDER BY sort ASC,dateline DESC LIMIT 8",array(":uniacid"=>$_W['uniacid'],':cid'=>$row['id'],':isshow'=>'1'));
					if(empty($row['list'])){
						unset($list[$key]);
					}else{
						$lists[] = $row;
					}

			}
		}
		include $this->template('index');
	}

	public function doWebRecommend(){
		global $_W,$_GPC;
		$id= $_GPC['id'];
		$rid= $_GPC['rid'];
		$single = pdo_fetch("select * from ".tablename('wxz_wzb_live_setting')." where id = ".$id);
		$recommend= $_GPC['recommend'];
		pdo_update('wxz_wzb_live_setting',array('recommend'=>$recommend),array('id' => $single['id']));
		$return_url = $_W['siteroot'] .'web'.str_replace("./","/",$this->createWebUrl('liveList',array('rid'=>$rid)));
        header("location: $return_url");
        exit;
	}

	public function doMobileClist(){
		global $_W,$_GPC;
		$cid = intval($_GPC['cid']);
		if(empty($cid)){
			message('分类错误！',$this->createMobileUrl('index'),'error');
		}
		$category = pdo_fetch("SELECT `title`,`id` FROM ".tablename('wxz_wzb_category')." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$cid));
		$lists = pdo_fetchall("SELECT * FROM ".tablename('wxz_wzb_live_setting')." WHERE uniacid=:uniacid AND cid=:cid AND isshow=:isshow",array(':uniacid'=>$_W['uniacid'],':cid'=>$cid,':isshow'=>'1'));
		include $this->template('clist');
	}

	public function doMobileIndex2() {
		global $_W,$_GPC;
		$sub_openid = $_GPC['sub_openid'];
		$rid = $_GPC['rid'];
		$time = $_GPC['time'];
		$share_uid = $_GPC['share_uid'];
		
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		
		$uid = $this->auths();
		
		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );
		$this->intoroom($rid,$user);
		$viewer = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_viewer') . ' WHERE `uid` = :uid AND `rid` = :rid', array(':uid' => $uid,':rid' => $rid) );

		if (!$user['sub_openid']){
			if($sub_openid ){
				$data=array(
					'sub_openid'=>$sub_openid
				);
				pdo_update('wxz_wzb_user', $data, array('id' => $uid));
			}
		}
		
		$LivePic = pdo_fetchAll('SELECT * FROM ' . tablename('wxz_wzb_live_pic').' where rid = '.$rid.' order by id asc');
		$Comments = pdo_fetchAll('SELECT * FROM ' . tablename('wxz_wzb_comment') .' where is_auth = 1 and rid = '.$rid.' order by id asc');
		$Comment = array_reduce($Comments, create_function('$v,$Comments', '$v[$Comments["id"]]=$Comments;return $v;'));  
		if(!empty($Comment)){
			foreach($Comment as $key => $v){
				if(isset($v['toid']) && $v['toid']>0){
					if(isset($Comment[$v['toid']])){
						$Comment[$v['toid']]['reply'][] = $v;
					}
					unset($Comment[$key]);
				}
			}
		}

		$item = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_live_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));

		$packet = pdo_fetch("select * from ".tablename('wxz_wzb_red_packet')." where rid = ".$rid." and uniacid = ".$_W['uniacid']);
		if($share_uid && $item['reward']==1 && $packet['type']==2){
			$this->addAmount($share_uid,$rid);
		}

		$spread = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_spread_adv') . ' WHERE `uniacid` = :uniacid and `rid` = :rid and `type` = :type', array(':uniacid' => $_W['uniacid'],':rid' => $rid,':type' => 1));

		$roll = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_roll_adv') . ' WHERE `uniacid` = :uniacid and `rid` = :rid and `type` = :type', array(':uniacid' => $_W['uniacid'],':rid' => $rid,':type' => 1));

		$paylog= pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_paylog') . ' WHERE `uniacid` = :uniacid AND `uid` = :uid AND `rid` = :rid AND type =:type AND lid = :lid', array(':uniacid' => $_W['uniacid'],':uid' => $uid,':rid' => $rid,':type' => 1,':lid' => $item['id']) );
		if($spread['type']==1 && $time==0){
			include $this->template('spread');exit;
		}
		if(!$paylog){
			$paylog['rid'] = $rid;
			$paylog['amount'] = $item['amount'];
			$paylog['uid'] = $uid;
			$paylog['uniacid'] = $_W['uniacid'];
			$paylog['lid'] = $item['id'];
			$paylog['type'] = 1;
			$paylog['status'] = 0;
			$paylog['dateline'] = time();
			$paylog['intotime'] = time();
			pdo_insert('wxz_wzb_paylog', $paylog);

		}
		if($item['limit'] == 3){
			$limit_time = $paylog['intotime'] + $item['delayed'];
		}
		if(($item['limit'] == 1 && $item['password'] != $viewer['password']) || ($item['limit'] == 2 && $paylog['status']!=1) || ($item['limit'] == 3 && $paylog['status']!=1 && ($limit_time - time())<0)){
			include $this->template('verify');exit;
		}

		

		$setting = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));
		

		$sql='SELECT b.nickname,b.id,b.headimgurl,a.amount FROM ' . tablename('wxz_wzb_share') . ' as a inner JOIN ' . tablename('wxz_wzb_user') . ' AS b ON a.help_uid=b.id inner join ' . tablename('wxz_wzb_viewer') . ' as c on b.id=c.uid WHERE a.uniacid = '.$_W['uniacid'].' and a.share_uid='.$uid.' and c.rid='.$rid.' order by a.id desc';
		$help_user = pdo_fetchall($sql);

		$total_num = $this->num($item);


		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$lasdCom = end($Comments);
		$lasdLive = end($LivePic);
		$menu = pdo_fetchAll('select * from '.tablename('wxz_wzb_live_menu').' where rid='.$rid.' and isshow=1 order by sort desc');

		$menunums = count($menu);

		$list = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_live_video_type')." WHERE uniacid=:uniacid AND rid=:rid",array(':uniacid'=>$_W['uniacid'],':rid'=>$rid));
		
		if($list){
			$list['settings'] = iunserializer($list['settings']);
		}
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		include $this->template('index2');
	}

	public function doMobilePass(){
		global $_GPC, $_W;
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$pass=$_GPC['password'];
		$rid = $_GPC['rid'];

		$item = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_live_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));

		if($item['limit'] == '1'){
			if($item['password'] == $pass){
				$data['password'] = $pass;
				pdo_update('wxz_wzb_viewer', $data, array('rid' => $rid,'uid' => $uid));
				$result['status'] = 1;
				$result['msg'] = '密码正确';
				echo json_encode($result);
			}else{
				$result['status'] = -1;
				$result['msg'] = '密码错误';
				echo json_encode($result);
			}
			
		}else{
			$result['status'] = 1;
			$result['msg'] = '不需要密码';
			echo json_encode($result);
		}
	}

	public function doMobilePay(){
		global  $_W, $_GPC;

		$rid = intval($_GPC['rid']);
		$fee = intval($_GPC['fee']);
		$lid = intval($_GPC['lid']);
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];

		$log = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_paylog')." WHERE rid = :rid AND uid = :uid AND uniacid = :uniacid AND type = :type", array(':rid' => $rid,':uid' => $uid,':type' => 1,':uniacid' => $_W['uniacid']));

		if(!$log){
			$log['rid'] = $rid;
			$log['amount'] = $fee;
			$log['uid'] = $uid;
			$log['uniacid'] = $_W['uniacid'];
			$log['lid'] = $lid;
			$log['type'] = 1;
			$log['status'] = 0;
			$log['dateline'] = time();
			
			pdo_insert('wxz_wzb_paylog', $log);
			$logid=pdo_insertid();
		}else{
			$logid = $log['id'];
		}

		$fee = $fee/100;
		$params['tid'] = $logid;
		$params['user'] = $_W['fans']['from_user'];
		$params['fee'] = $fee;
		$params['title'] = $_W['account']['name'];
		$params['ordersn'] = date('YmdHis').'-'.$_W['member']['uid'];

		$this->pay($params);exit;
	}

	public function payResult($params) {
		$log = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_paylog')." WHERE id = :id", array(':id' => $params['tid']));
		if ($params['result'] == 'success' && $params['from'] == 'notify') {
			$fee = $params['fee'];
			$total_fee = $fee;
			$data = array('status' => $params['result'] == 'success' ? 1 : -1);
			if ($params['type'] == 'wechat') {
				$data['transid'] = $params['tag']['transaction_id'];
			}
			pdo_update('wxz_wzb_paylog', $data, array('id' => $params['tid']));
			if ($params['fee'] != $log['fee']) {
				exit('用户支付的金额与订单金额不符合');
			}
		}

		if ($params['from'] == 'return') {
			if ($params['result'] == 'success') {
				message('支付成功！', $this->createMobileUrl('index', array('rid' => $log['rid'])), 'success');
			} else {
				message('支付失败！', $this->createMobileUrl('index', array('rid' => $log['rid'])), 'error');
			}
		}
	}

	public function ipAuth($getAdr,$type,$rid,$ip){
        global $_GPC, $_W;
        $allowArea = explode(",",$getAdr);
				
		
        $ip = IP::find($ip);

        switch($type){
            case 1:
                if(!in_array($ip[2],$allowArea)){
                    return false;
                }
				break;
            case 2:
                if(in_array($ip[2],$allowArea)){
                    return false;
                }
				break;
            default:
				return true;
        }
		return true;
    }

	//发送红包
	public function doMobileSends(){
		
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $rid = $_GPC['rid'];
        $hb_id = $_GPC['hb_id'];
		$hb_msg = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_comment') . ' WHERE `uniacid` = :uniacid AND `id` = :hb_id AND `rid` = :rid', array(':uniacid' => $_W['uniacid'],':hb_id' => $hb_id,':rid' => $rid) );

		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];

		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		$viewer = pdo_fetch("select * from ".tablename('wxz_wzb_viewer')." where rid=".$rid." and uid=".$user['id']);
		$log = pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_tx')." WHERE rid = :rid AND uid = :uid AND uniacid = :uniacid AND type = :type AND fromid = :fromid", array(':rid' => $rid,':uid' => $uid,':fromid' => $hb_msg['id'],':type' => 3,':uniacid' => $_W['uniacid']));
			
		if(!$log){
			$res['type']=-1;//未关注
			$res['msg']='你已经领过该红包';//未关注
			echo json_encode($res);
			exit();
		}

		if($hb_msg['num']==$hb_msg['send_num']){
			$res['type']=-1;//未关注
			$res['msg']='红包已发完';//未关注
			echo json_encode($res);
			exit();
		}
		$fee = $this->randBonus(($hb_msg['amount']-$hb_msg['yifa_amount']),($hb_msg['num']-$hb_msg['send_num']),$hb_msg['type']);
		if($hb_msg['yifa_amount']>=$hb_msg['amount'] || $hb_msg['send_num']>=$hb_msg['num']){
			$res['type']=-1;
			$res['msg']='红包已领完';
			echo json_encode($res);
			exit();
		}
		$fee = $fee[0];
		if($fee <1){
			$fee = 1;
		}
		if($fee>$hb_msg['amount']){
			$res['type']=-1;
			$res['msg']='你的提现有点多';
			echo json_encode($res);
			exit();
		}
		
		$rec = array();
		$rec['uid'] = $user['id'];
		$rec['uniacid'] = $_W['uniacid'];
		$rec['fee'] = floatval($fee/100);
		$rec['dateline'] = TIMESTAMP;
		$rec['rid'] = $rid;
		$rec['fromid'] = $hb_msg['id'];
		$rec['type'] = 3;
		pdo_insert('wxz_wzb_tx', $rec);

		$logid=pdo_insertid();
		$user_amount['amount'] = $fee+$viewer['amount'];
		pdo_update('wxz_wzb_viewer', $user_amount, array('uid'=>$uid,'rid'=>$rid));
		pdo_update('wxz_wzb_comment', array('send_num'=>$hb_msg['send_num']+1,'yifa_amount'=>$hb_msg['yifa_amount']+$fee), array('id'=>$hb_msg['id']));
		$data2=array(
			'uniacid'=>$_W['uniacid'],
			'uid'=>$uid,
			'ip'=>$_W['clientip'],
			'is_auth'=>1,
			'nickname'=>$user['nickname'],
			'headimgurl'=>$user['headimgurl'],
			'rid'=>$rid,
			'content'=>$_GPC['content'],
			'toid'=>$hb_msg['id'],
			'touid'=>$hb_msg['uid'],
			'num'=>1,
			'ispacket'=>1,
			'type'=>$hb_msg['type'],
			'tonickname'=>$hb_msg['nickname'],
			'amount'=>$fee,
			'toheadimgurl'=>$hb_msg['headimgurl'],
			'dateline'=>time()
		);
		pdo_insert('wxz_wzb_comment',$data2);
		$html = '<p><img  src="'.MODULE_URL.'template/mobile/img/mini_hongbao.png" />'.$user['nickname'].'领取了<span style="color:#FF0000;">红包</span></p>';
		$res['type']=1;//未关注
		$res['msg']='领取成功';//未关注
		$res['rhtml']=$html;//未关注
		$r = $this->alrecy($rid,$hb_id);
		echo json_encode($res+$r);
    }

	public function alrecy($rid,$hb_id){
		global $_W,$_GPC;

		$hb_msg = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_comment') . ' WHERE `uniacid` = :uniacid AND `id` = :hb_id AND `rid` = :rid', array(':uniacid' => $_W['uniacid'],':hb_id' => $hb_id,':rid' => $rid) );

		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];

		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		$my = pdo_fetchAll("SELECT * FROM ".tablename('wxz_wzb_comment')." WHERE rid = :rid AND uid = :uid AND uniacid = :uniacid AND toid = :toid", array(':rid' => $rid,':uid' => $uid,':toid' => $hb_id,':uniacid' => $_W['uniacid']));

		$viewer = pdo_fetch("select * from ".tablename('wxz_wzb_viewer')." where rid=".$rid." and uid=".$user['id']);
		$log = pdo_fetchAll("SELECT * FROM ".tablename('wxz_wzb_comment')." WHERE rid = :rid AND uniacid = :uniacid AND toid = :toid", array(':rid' => $rid,':toid' => $hb_id,':uniacid' => $_W['uniacid']));
		foreach($log as $key => $v){
			$html .= '<li>';
			$html .= '<div class="hongbao_list_heard"><img src="'.$v['headimgurl'].'" /></div>';
			$html .= '<div class="hongbao_list_font">'.($v['amount']/100).'元</div>';
			$html .= '<div style="padding-left:70px; line-height:40px; font-size:14px;">'.$v['nickname'].'</div>';
			$html .= '</li>';
		}
		$name = $hb_msg['nickname'];
		$name = $hb_msg['headimgurl'];
		$res['name']=$hb_msg['nickname'];
		$res['amount']=$my['amount'];
		$res['num']=$hb_msg['send_num'].'/'.$hb_msg['num'];
		$res['headimgurl']=$hb_msg['headimgurl'];
		$res['hhtml']=$html;
		return $res;
	}
	
	public function intoroom($rid,$user){
		global $_W,$_GPC;
		$viewer = pdo_fetch("select * from ".tablename('wxz_wzb_viewer')." where rid=".$rid." and uid=".$user['id']);
		if(!$viewer){
			$data['rid'] = $rid;
			$data['uid'] = $user['id'];
			$data['dateline'] = time();
			pdo_insert('wxz_wzb_viewer',$data);
		}
	}

	//认证
	public function doMobileAuth2() {
        global $_W,$_GPC;
		$sub_openid = $_GPC['sub_openid'];
		$share_uid = $_GPC['share_uid'];
		$rid = $_GPC['rid'];
		$back_url = isset($_GPC['back_url']) ? $_GPC['back_url'] : 'index';
		
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$item = pdo_fetch("select * from ".tablename('wxz_wzb_setting')." where rid = ".$rid." and uniacid = ".$_W['uniacid']);

        $return_url = $_W['siteroot'].'app/' . (substr($this->createMobileurl($back_url,array('sub_openid'=>$sub_openid,'share_uid'=>$share_uid,'rid'=>$rid)), 2));
        if(empty($_GPC['code'])){

			$back_url = $_W['siteroot'].'app/' . (substr($this->createMobileurl('auth',array('sub_openid'=>$sub_openid,'share_uid'=>$share_uid,'rid'=>$rid,'back_url'=>$back_url)), 2));
			//服务号appid 待创建
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$item['loan_appid']."&redirect_uri=".urlencode($back_url)."&response_type=code&scope=snsapi_userinfo&is_authe=STATE#wechat_redirect";
			header("location:".$url);
            exit;
        }
        $param=array();
        $param ['appid']=$item['loan_appid']; //服务号appid
        $param ['secret'] = $item['loan_secret'];//服务号secret
        $param ['code'] = $_GPC['code'];
        $param ['grant_type'] = 'authorization_code';
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
		
        $content = file_get_contents ( $url );
        $content = json_decode ( $content, true );        
		
        $param=array();
        $param ['access_token'] = $content ['access_token'];
        $param ['openid'] = $content ['openid'];
        $param ['lang'] = 'zh_CN';
        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query ( $param );
        $content = file_get_contents ( $url );
        $wxuser = json_decode ( $content, true );

		if(!$wxuser['openid']){
            header("location: $url");
            exit;
        }

        $user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `openid` = :openid', array(':uniacid' => $_W['uniacid'],':openid' => $wxuser['openid']) );
		
		if(!$sub_openid && $user['sub_openid']){
			$sub_openid = $user['sub_openid'];
		}
        $data=array(
            'uniacid'=>$_W['uniacid'],
            'nickname'=>$wxuser['nickname'],
            'headimgurl'=>$wxuser['headimgurl'],
            'province'=>$wxuser['province'],
            'ip'=>$_W['clientip'],
            'city'=>$wxuser['city'],
            'sex'=>$wxuser['sex'],
            'dateline'=>time(),
            'sub_openid'=>$sub_openid,
            'openid'=>$wxuser['openid']
        );
        if($user){
            pdo_update('wxz_wzb_user', $data, array('id' => $user['id']));
        }else{
            pdo_insert('wxz_wzb_user', $data);
            $user['uid']=pdo_insertid();
        }
        isetcookie('wxz_wzb_user'.$_W['uniacid'], $user['id']);
		
        header("location: $return_url");
        exit;
    }

	public function ip(){
		load()->func('communication');
		$content = ihttp_request("http://ip.taobao.com/service/getIpInfo.php?ip=".CLIENT_IP);		
		$info = @json_decode($content['content'], true);
		return $info;
	}


	public function auths(){
		global $_W,$_GPC;

		$sub_openid = $_GPC['sub_openid'];
		$share_uid = $_GPC['share_uid'];
		$rid = $_GPC['rid'];
		$back_url = isset($_GPC['back_url']) ? $_GPC['back_url'] : 'index';
		
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$settings = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));

		$gz_url = $settings['attention_url'];

		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if (strpos($user_agent, 'MicroMessenger') === false) {
			$openid = $ip = CLIENT_IP;
			
			$user =  pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_user')." WHERE sub_openid = '".$ip."' AND uniacid=".$_W['uniacid']);

			if(empty($user)){
				if(empty($ip)){
					header("location:$gz_url");
					exit;
				}
				$ip_address = $this->ip();

				if($ip_address['code']!=0){
						header("location:$gz_url");
						exit;
				}
				$web_data = array(
					'sub_openid' =>$ip,
					'uniacid'=>$_W['uniacid'],
					'province'=>mb_substr($ip_address['data']['region'],0,-1),
					'ip'=>$ip,
					'city'=>mb_substr($ip_address['data']['city'],0,-1),
					'dateline'=>time(),
					'headimgurl'=> tomedia($settings['no_avatar']),
					'nickname'=>empty($ip_address['data']['region'])?'网友':$ip_address['data']['region'].'网友',
					'sex'=>0,
				);
				
				pdo_insert('wxz_wzb_user',$web_data); 
				$user_id = pdo_insertid();
				
				$user =  pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_user')." WHERE id = :id  AND uniacid=:uniacid",array(':id'=>$user_id,':uniacid' =>$_W['uniacid']));
				
			}
			
			isetcookie('wxz_wzb_user'.$_W['uniacid'], $user['id'],84600);

			return $user['id'];
		} else {
			$openid = $_W['openid'];
					
			if(empty($openid)){
				header("location:$gz_url");
				exit;
			}
			$user =  pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_user')." WHERE openid = :openid AND uniacid=:uniacid",array(':openid' =>$openid,':uniacid' =>$_W['uniacid']));
			
			if(empty($user) || $_W['fans']['follow']==0){
				$data = array(
					'sub_openid' =>$openid,
					'uniacid'=>$_W['uniacid'],
					'dateline'=>time(),
				);
				if($_W['account']['level']<3){
					load()->model('mc');
					$oauth_user = mc_oauth_userinfo();
					if (!is_error($oauth_user) && !empty($oauth_user) && is_array($oauth_user)) {
								$userinfo = $oauth_user;
					}else{
								message("借用oauth失败");
					}
				}elseif($_W['account']['level']==3){
					if($settings['gz_must']=='0'){
									load()->model('mc');
									$oauth_user = mc_oauth_userinfo();
									if (!is_error($oauth_user) && !empty($oauth_user) && is_array($oauth_user)) {
												$userinfo = $oauth_user;
									}else{
												message("借用oauth失败");
									}
					}else{
						if($_W['fans']['follow']=='1'){
									$userinfo = $this->get_follow_fansinfo($openid);
									if($userinfo['subscribe']!='1'){
										message('获取粉丝信息失败');
									}
						}else{
										header("location:$gz_url");
										exit;
						}
					}
				}else{
					
						if($_W['fans']['follow']=='1'){
								$userinfo = $this->get_follow_fansinfo($openid);

								if($userinfo['subscribe']!='1'){
									
									message('获取粉丝信息失败');
								}
						}else{
								if($settings['gz_must']=='0'){
									$oauth_user = mc_oauth_userinfo();
									if (!is_error($oauth_user) && !empty($oauth_user) && is_array($oauth_user)) {
												$userinfo = $oauth_user;
									}else{
												message("借用oauth失败");
									}
								}else{
									header("location:$gz_url");
									exit;
								}
						}
				}
				if(!empty($userinfo['headimgurl'])){
					 $data['headimgurl'] = $userinfo['headimgurl'];
				}else{
					if(empty($userinfo['headimgurl'])){
					 $data['headimgurl'] = tomedia($settings['no_headimgurl']);
					}else{
						$data['headimgurl'] = $userinfo['headimgurl'];
					}
				}
				if(empty($userinfo['sex'])){
					$data['sex'] = '0';
				}else{
					$data['sex'] = $userinfo['sex'];
				}
				if(!empty($userinfo['nickname'])){
					$data['nickname'] = $userinfo['nickname'];
				}else{
					$data['nickname'] = '微信昵称无法识别';
				}
				$data['openid'] = $userinfo['openid'];
				$data['province']=$userinfo['province'];
				$data['ip']=$_W['clientip'];
				$data['city']=$userinfo['city'];
				if(empty($user)){
					pdo_insert('wxz_wzb_user',$data); 
					$user_id = pdo_insertid();
					$user =  pdo_fetch("SELECT * FROM ".tablename('wxz_wzb_user')." WHERE id = :id  AND uniacid=:uniacid",array(':id'=>$user_id,':uniacid' =>$_W['uniacid']));
				}
			}
			
		}

		isetcookie('wxz_wzb_user'.$_W['uniacid'], $user['id'],84600);
			return $user['id'];
	}



	public function get_follow_fansinfo($openid){
		global $_W,$_GPC;
		$access_token = $this->getAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		load()->func('communication');
		$content = ihttp_request($url);		
		$info = @json_decode($content['content'], true);
		return $info;
	}

	public  function  getAccessToken () {
		global $_W;
		load()->classs('weixin.account');
		$accObj = WeixinAccount::create($_W['acid']);
		$accObj->clearAccessToken($_W['acid']);
		$access_token = $accObj->fetch_token();
		return $access_token;
	}

	

	function randBonus($bonus_total=0, $bonus_count=3, $bonus_type=1){
	  $bonus_items  = array(); // 将要瓜分的结果
	  $bonus_balance = $bonus_total; // 每次分完之后的余额
	  $bonus_avg   = number_format($bonus_total/$bonus_count, 2); // 平均每个红包多少钱
	  $i       = 0;
	  while($i<$bonus_count){
		if($i<$bonus_count-1){
		  $rand      = $bonus_type?(rand(1, $bonus_balance*100-1)/100):$bonus_avg; // 根据红包类型计算当前红包的金额
		  $bonus_items[] = $rand;
		  $bonus_balance -= $rand;
		}else{
		  $bonus_items[] = $bonus_balance; // 最后一个红包直接承包最后所有的金额，保证发出的总金额正确
		}
		$i++;
	  }
	  return $bonus_items;
	}

	

	protected function num($item){
		global $_W,$_GPC;
		
		$total = $item['total_num']== 0 ? $item['base_num'] : $item['total_num'];
		$float= $item['num_float'];
		$num = rand(0,$float);
		if($item['float_type']==1){
			$data = array(
				'total_num'=>$total+$num+1
			);
		}else{
			$Symbol = array('-','+');
			if(array_rand($Symbol) == 1){
				$data = array(
					'total_num'=>$total+$num+1
				);
			}else{
				$data = array(
					'total_num'=>$total-$num+1
				);
			}
		}
		$data['real_num'] = $item['real_num'] + 1;
		pdo_update('wxz_wzb_live_setting', $data, array('id' => $item['id']));
		return $data['total_num'];
	}

	protected function addAmount($share_uid,$rid){
		global $_W,$_GPC;
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $share_uid) );

		$viewer = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_viewer') . ' WHERE `uid` = :uid AND `rid` = :rid', array(':uid' => $uid,':rid' => $rid) );

		
		$log = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_share') . ' WHERE `uniacid` = :uniacid AND `share_uid` = :share_uid AND `help_uid` = :help_uid AND `rid` = :rid', array(':uniacid' => $_W['uniacid'],':share_uid' => $share_uid,':help_uid' => $uid,':rid' => $rid) );

		$setting = pdo_fetch("select * from ".tablename('wxz_wzb_red_packet')." where rid = ".$rid." and uniacid = ".$_W['uniacid']);

		$set = pdo_fetch("select * from ".tablename('wxz_wzb_setting')." where rid=".$rid." and uniacid = ".$_W['uniacid']);

		$auth = $this->ipAuth($set['getip_addr'],$set['getip'],$rid,$user['ip']);
			
		if(!$log && $uid != $share_uid && ($setting['pool_amount']-$setting['send_amount'])>0 && $auth){
			$data['share_uid'] = $share_uid;
			$data['uniacid'] = $_W['uniacid'];
			$data['help_uid'] = $uid;
			$data['rid'] = $rid;
			$data['dateline'] = time();
			if($setting['type']==2){
				$data['amount'] = rand($setting['reward_amount_min'],$setting['reward_amount_max']);
				if($data['amount']>($setting['pool_amount']-$setting['send_amount'])){
					$data['amount'] = $setting['pool_amount']-$setting['send_amount'];
				}
				pdo_update('wxz_wzb_viewer',array('amount'=>$viewer['amount']+$data['amount']),array('uid'=>$uid,'rid'=>$rid)); 
				
			}else{
				$data['amount'] = 0;
			}

			pdo_insert('wxz_wzb_share', $data);
			pdo_update('wxz_wzb_red_packet',array('send_amount'=>$setting['send_amount']+$data['amount']),array('id' => $setting['id'])); 
		}
		
	}

	public function doMobileGetlivepic(){
		global $_W,$_GPC;
		$lastid = intval($_GPC['lastid']);
		$replyid = intval($_GPC['replyid']);
		$rid = intval($_GPC['rid']);
		$LivePic = pdo_fetchAll('SELECT * FROM ' . tablename('wxz_wzb_live_pic') .' where id>'.$lastid .' and rid='.$rid.' order by id asc');
		$setting = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));
		$lhtml = '';
		foreach($LivePic as $key => $v){
			$lhtml .='<div class="detail">';
			$lhtml .='<div class="live_title">';
			$lhtml .='<h3>'.$v['title'].'</h3>';
			$lhtml .='<span class="time">'.date("H:i",$v['dateline']).'</span>';
			$lhtml .='<div class="clear"></div>';
			$lhtml .='</div>';
			$lhtml .='<figure>';
			$lhtml .='<figcaption>'.$v['content'].'</figcaption>';
			$lhtml .='</figure>';
			$lhtml .='</div> ';
		}
	
		$rhtml = '';
		$mrhtml = array();
		
		$Comment = pdo_fetchAll('SELECT * FROM ' . tablename('wxz_wzb_comment') .' where id>'.$replyid .' and is_auth = 1 and rid ='.$rid.' order by id asc');

		foreach($Comment as $k => $val){
			if($val['toid']){
				if($val['ispacket']==1){
					$mrhtml[$val['toid']] .='<p style="font-size:12px; line-height:18px; font-weight:400; margin:10px; display:block; text-align:center;  border-radius:6px; background-color:#cecece; color:#FFFFFF">';
					$mrhtml[$val['toid']] .='<img  src="'.MODULE_URL.'template/mobile/img/mini_hongbao.png"  style=" width:14px; height:14px; vertical-align:middle; " /> ';
					$mrhtml[$val['toid']] .=$val['nickname'].'领取了<span style="color:#FF0000;">红包</span></p>';
				}else{
					$mrhtml[$val['toid']] .='<li>';
					$mrhtml[$val['toid']] .='<div class="body" style="margin:0px 14px;">';
					$mrhtml[$val['toid']] .='<div class="text">';
					$mrhtml[$val['toid']] .='<div class="title"  style="position:relative;"><img class="avatar" src="'.$val['headimgurl'].'" alt="avatar">'.$val['nickname'].'<span style="position:absolute;right:0px;">'.date("H:i",$val['dateline']).'</span></div>';
					$mrhtml[$val['toid']] .='<div class="txt">'.$val['content'].'</div>';
					$mrhtml[$val['toid']] .='<div class="tm">';
					$mrhtml[$val['toid']] .='<div class="fl"></div>';
					$mrhtml[$val['toid']] .='<div class="fr"></div>';
					$mrhtml[$val['toid']] .='</div>';
					$mrhtml[$val['toid']] .='</div>';
					$mrhtml[$val['toid']] .='</div>';
					$mrhtml[$val['toid']] .='</li>';
				}
				
			}else{
				if($val['ispacket']==1){	

					$rhtml .='<li class="d-flex">';
					$rhtml .='<div class="marry-chat-content clearfix d-flex">';
					$rhtml .='<img src="" alt="" class="userphoto">';
					$rhtml .='<div class="flex">';
					$rhtml .='<span class="nickname">';
					$rhtml .=$val['nickname'].'</span>';
					$rhtml .='<div class="msg-content" style="background:none; padding:6px 6px 6px 0;">';
					$rhtml .='<a href="#"  onClick="$(".on_liaotian_hb").show();" style="display:block;"><img src="'.MODULE_URL.'template/mobile/img/hb_img_0001.png" width="100%" height="auto" alt="" /></a>';
					$rhtml .='<div id="hongbao_"'.$val['id'].'>';
					$rhtml .='</div>';
					$rhtml .='<div class="nhzb-redline-all">';
					$rhtml .='<div class="nhzb-redline"></div>';
					$rhtml .='<div class="nhzb-hen"></div>';
					$rhtml .='</div>';
					$rhtml .='</div>';
					$rhtml .='</div>';
					$rhtml .='</div> '; 
					$rhtml .='</li>';	
	
					
					
				}else{
					$rhtml .='<li class="d-flex" data-msgtype="1" attrtime="'.$val['dateline'].'" data-addtime="'.date('Y/m/d H:i',$val['dateline']).'" attr-uid="'.$val['uid'].'">';
					$rhtml .='<div class="marry-chat-content clearfix d-flex">';
					
					$rhtml .='<img src="'.$val['headimgurl'].'" alt="" class="userphoto">';
					$rhtml .='<div class="flex">';
					$rhtml .='<span class="nickname">';
					$rhtml .=$val['nickname'];
					$rhtml .='</span>';
					$rhtml .='<div class="msg-content">';
					$rhtml .=$val['content'];
					$rhtml .='<div class="nhzb-redline-all">';
					$rhtml .='<div class="nhzb-redline"></div><div class="nhzb-hen"></div></div></div></div></div></li>';
				}
			}
			
		}
		$lasdCom = end($Comment);
		$lasdLive = end($LivePic);
		$result['status'] = 1;
		$result['rhtml'] = $rhtml;
		$result['lhtml'] = $lhtml;
		$result['mrhtml'] = $mrhtml;
		$result['replyid'] = empty($Comment) ? $replyid : $lasdCom['id'];
		$result['lastid'] = empty($LivePic) ? $lastid : $lasdLive['id'];
		echo json_encode($result);
	}

	public function doMobileSub(){
		global $_W,$_GPC;
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$rid = intval($_GPC['rid']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_live_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));
		if(!$uid){
			$msg = '请关注公众号！';
			echo json_encode($msg);
            exit;
        }

		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		if(!$user){
			$msg = '请关注公众号！';
			echo json_encode($msg);
            exit;
        }

		$touser = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_comment') . ' WHERE `uniacid` = :uniacid AND `id` = :id and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':id' => $_GPC['toid'],':rid' => $rid) );
		

		$data=array(
			'uniacid'=>$_W['uniacid'],
			'uid'=>$uid,
			'ip'=>$_W['clientip'],
			'is_auth'=>$item['is_auth']==1 ? 2 : 1,
			'nickname'=>$user['nickname'],
			'headimgurl'=>$user['headimgurl'],
			'rid'=>$rid,
			'content'=>$_GPC['content'],
			'toid'=>$_GPC['toid'],
			'touid'=>$touser['uid'],
			'tonickname'=>$touser['nickname'],
			'toheadimgurl'=>$touser['headimgurl'],
			'dateline'=>time()
		);
		
		pdo_insert('wxz_wzb_comment', $data);
		$id=pdo_insertid();
		$item = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_live_setting') . ' WHERE `uniacid` = :uniacid and `rid` = :rid', array(':uniacid' => $_W['uniacid'],':rid' => $rid));
		
		if($id){
			if($item['is_auth'] == '1'){
				$msg = '提交成功，审核成功后显示';
			}else{
				$msg = '提交成功';
			}
			
		}else{
			$msg = '提交失败，请联系管理员';
		}
		echo json_encode($msg);
		exit;
	}

	

	public function doMobileShare(){
		global $_W,$_GPC;
		
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$rid=$_GPC['rid'];
		
		if(!$uid){
			$result = array('type'=>'-1','msg'=>'请关注');
		}

		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		$viewer = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_viewer') . ' WHERE `uid` = :uid AND `rid` = :rid', array(':uid' => $uid,':rid' => $rid) );

		$setting = pdo_fetch("select * from ".tablename('wxz_wzb_red_packet')." where rid=".$rid." and uniacid = ".$_W['uniacid']);
		$item = pdo_fetch("select * from ".tablename('wxz_wzb_live_setting')." where rid=".$rid." and uniacid = ".$_W['uniacid']);
		$set = pdo_fetch("select * from ".tablename('wxz_wzb_setting')." where rid=".$rid." and uniacid = ".$_W['uniacid']);
		if(!$user || $user['sub_openid'] ==""){
			$result = array('type'=>'-1','msg'=>'请关注');
		}
		$auth = $this->ipAuth($set['getip_addr'],$set['getip'],$rid,$user['ip']);
		if($item['reward']== 1 && $setting['type']== 1 && $viewer['share']==0 && ($setting['pool_amount']-$setting['send_amount'])>=100 && $auth){
			if($viewer['ispay']==0){
				$data=array(
					'share'=>'1',
					'amount'=>rand($setting['min'],$setting['max'])
				);
				if($data['amount']>($setting['pool_amount']-$setting['send_amount'])){
					$data['amount'] = $setting['pool_amount']-$setting['send_amount'];
				}
				pdo_update('wxz_wzb_red_packet',array('send_amount'=>$setting['send_amount']+$data['amount']),array('id' => $setting['id'])); 
				$res = pdo_update('wxz_wzb_viewer', $data, array('uid'=>$uid,'rid'=>$rid));
				$r = $this->Fee('1',$rid);
				if($r['type']== 1 ){
					$result = array('type'=>'1','msg'=>'分享成功');
				}else{
					$result = array('type'=>'-1','msg'=>$r['msg']);
				}
			}else{
				$result = array('type'=>'1','msg'=>'分享成功');
			}
		}else{
			$data=array(
				'share'=>'1'
			);
			$res = pdo_update('wxz_wzb_viewer', $data, array('uid'=>$uid,'rid'=>$rid));
			$result = array('type'=>'1','msg'=>'分享成功');
		}
		echo json_encode($result);exit;

	}

	public function Fee($type,$rid){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
					
		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		$viewer = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_viewer') . ' WHERE `uid` = :uid AND `rid` = :rid', array(':uid' => $uid,':rid' => $rid) );


		$api = pdo_fetch("select * from ".tablename('wxz_wzb_red_packet')." where rid=".$rid." and uniacid = ".$_W['uniacid']);
		//先判断是否关注
		if($user['sub_openid']==''){
			$res['type']=-10;//未关注
			$res['msg']='请先关注在参加活动';//未关注
			//exit();
		}else{
			if($viewer['ispay']=='1'){
				$res['type']=-1;
				$res['msg']='您已提现';
				echo json_encode($res);
				exit();
			}
			if($viewer['share']=='0'){
				$res['type']=-1;
				$res['msg']='请先分享';
				echo json_encode($res);
				exit();
			}
			if($viewer['amount']>$api['pool_amount']){
				$res['type']=-1;
				$res['msg']='你的提现有点多';
				echo json_encode($res);
				exit();
			}
			$fee = $viewer['amount'] - $viewer['deposit'];
			if($fee<$api['withdraw_min']){
				if($api['type']==1 && $fee<100){
					$res = array('type'=>'1','msg'=>'分享成功');
				}else{
					$res['type']=-2;
					$res['msg']='您的提现金额少于'.($api['withdraw_min']/100).'元';
				}
				echo json_encode($res);exit;
			}
			
			$rec = array();
			$rec['uid'] = $user['id'];
			$rec['uniacid'] = $_W['uniacid'];
			$rec['fee'] = floatval($fee/100);
			$rec['dateline'] = TIMESTAMP;
			$rec['status'] = 'created';
			$rec['rid'] = $rid;
			$rec['type'] = $type;
			pdo_insert('wxz_wzb_packet_log', $rec);
			$logid=pdo_insertid();
			$actname = empty($api['actname']) ? '参与疯狂抢红包活动' : $api['actname'];

			if(empty($api['wishing'])){
				$wishing = '恭喜您,抽中了一个' . ($fee/100) . '元红包!';
			}else{
				$wishing = $api['wishing'] . ($fee/100) . '元红包!';
			}
			if(empty($api['sname'])){
				$send_name = $this->substr_cut($_W['account']['name'],30);
			}else{
				$send_name = $api['sname'];
			}
			$url                   = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
			$pars                  = array();
			$pars['nonce_str']     = random(32);
			$pars['mch_billno']    = $api['mchid'] . date('Ymd') . sprintf('%d', time());
			$pars['mch_id']        = $api['mchid'];
			$pars['wxappid']       = $api['appid'];
			$pars['nick_name']     = $_W['account']['name'];
			$pars['send_name']     = $send_name;
			$pars['re_openid']     = $user['openid'];
			$pars['total_amount']  = $fee;
			$pars['min_value']     = $pars['total_amount'];
			$pars['max_value']     = $pars['total_amount'];
			$pars['total_num']     = 1;
			$pars['wishing']       = $wishing;
			$pars['client_ip']     = $api['ip'];
			$pars['act_name']      = $actname;
			$pars['remark']        = '恭喜恭喜，您的' . ($fee/100) . '元红包已经发放，请注意查收';
			$pars['logo_imgurl']   = tomedia($api['logo']);
			
			ksort($pars, SORT_STRING);
			$string1 = '';
			foreach ($pars as $k => $v) {
				$string1 .= "{$k}={$v}&";
			}
			$string1 .= "key={$api['password']}";
			$pars['sign']              = strtoupper(md5($string1));
			$xml                       = array2xml($pars);
			$extras                    = array();
			$extras['CURLOPT_CAINFO']  = MODULE_ROOT . '/cert/rootca.pem.' . $uniacid;
			$extras['CURLOPT_SSLCERT'] = MODULE_ROOT . '/cert/apiclient_cert.pem.' . $uniacid;
			$extras['CURLOPT_SSLKEY']  = MODULE_ROOT . '/cert/apiclient_key.pem.' . $uniacid;

			load()->func('communication');
			$resp = ihttp_request($url, $xml, $extras);
			if (is_error($resp)) {
				$procResult = $resp;
			} else {
				$arr=json_decode(json_encode((array) simplexml_load_string($resp['content'])), true);
				$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
				$dom = new \DOMDocument();
				if ($dom->loadXML($xml)) {
					$xpath = new \DOMXPath($dom);
					$code = $xpath->evaluate('string(//xml/return_code)');
					$ret = $xpath->evaluate('string(//xml/result_code)');
					if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
						$procResult =  array('errno'=>0,'error'=>'success');
					} else {
						$error = $xpath->evaluate('string(//xml/err_code_des)');
						$procResult = array('errno'=>-2,'error'=>$error);
					}
				} else {
					$procResult = array('errno'=>-1,'error'=>'未知错误');
				}
			}

			if ($procResult['errno']!=0) {
				pdo_update('wxz_wzb_packet_log', array('status'=> $procResult['error']), array('id'=>$logid));
				$res['type']=-1;//未关注
				$res['msg']='提现失败';//未关注
				if($api['type']== 1){
					pdo_update('wxz_wzb_viewer', array('ispay'=>'2','rlog'=>$procResult['error']), array('uid'=>$uid,'rid'=>$rid));
				}else{
					pdo_update('wxz_wzb_viewer', array('rlog'=>$procResult['error']), array('uid'=>$uid,'rid'=>$rid));
				}
			}else{
				$res['type']=1;//未关注
				$res['msg']='提现成功';//未关注
				pdo_update('wxz_wzb_packet_log', array('status'=>'success'), array('id'=>$logid));
				if($api['type']== 1){
					$user_amount['ispay'] = 1;
					$user_amount['rlog'] = '发送成功';
					$user_amount['deposit'] = $fee+$viewer['deposit'];
					pdo_update('wxz_wzb_viewer', $user_amount, array('uid'=>$uid,'rid'=>$rid));
				}else{
					$user_amount['rlog'] = '发送成功';
					$user_amount['deposit'] = $fee+$viewer['deposit'];
					pdo_update('wxz_wzb_viewer', $user_amount, array('uid'=>$uid,'rid'=>$rid));
				}
				
			}	
		}
		return $res;
	}

	//发送红包
	public function doMobileSend(){
		
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $rid = $_GPC['rid'];
		$uid=$_GPC['wxz_wzb_user'.$_W['uniacid']];
		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		$viewer = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_viewer') . ' WHERE `uid` = :uid AND `rid` = :rid', array(':uid' => $uid,':rid' => $rid) );
		
		if($viewer['ispay']==1){
			$res['type']=1;//未关注
			$res['msg']='您已经提现过';//未关注
		}else{
			$res = $this->Fee('2',$rid);
		}
		echo json_encode($res);
    }

	public function doWebRedPacketSetting() {
		global $_W,$_GPC;
		$rid = intval($_GPC['rid']);
		$item = pdo_fetch("select * from ".tablename('wxz_wzb_red_packet')." where uniacid = ".$_W['uniacid']." and rid =".$rid);
		if($_W['ispost']) {
            load()->func('file');
            mkdirs(MODULE_ROOT  . '/cert',0777);
            $r = true;
            if (!empty($_GPC['apiclient_cert'])) {
                $ret = file_put_contents(MODULE_ROOT  . '/cert/apiclient_cert.pem.' . $_W['uniacid'], trim($_GPC['apiclient_cert']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['apiclient_key'])) {
                $ret = file_put_contents(MODULE_ROOT  . '/cert/apiclient_key.pem.' . $_W['uniacid'], trim($_GPC['apiclient_key']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['rootca'])) {
                $ret = file_put_contents(MODULE_ROOT  . '/cert/rootca.pem.' . $_W['uniacid'], trim($_GPC['rootca']));
                $r = $r && $ret;
            }
            if (!$r) {
                message('证书保存失败');
            }

            $data = array();
           // $data['set'] = trim($_GPC['password']);;
            $data['password'] = trim($_GPC['password']);;
            $data['uniacid'] = $_W['uniacid'];
            $data['appid'] = trim($_GPC['appid']);
            $data['secret'] = trim($_GPC['secret']);
            $data['mchid'] = intval($_GPC['mchid']);
            $data['ip'] = $_SERVER['SERVER_ADDR'];
			$data['min'] = intval($_GPC['min']);
			$data['max'] = intval($_GPC['max']);
            $data['sname'] = trim($_GPC['sname']);
            $data['wishing'] = trim($_GPC['wishing']);
            $data['actname'] = trim($_GPC['actname']);
            $data['rid'] = trim($_GPC['rid']);
            $data['logo'] = trim($_GPC['logo']);
            $data['rootca'] = trim($_GPC['rootca']);
            $data['type'] = trim($_GPC['type']);
            $data['withdraw_min'] = trim($_GPC['withdraw_min']);
            $data['reward_amount_min'] = trim($_GPC['reward_amount_min']);
            $data['reward_amount_max'] = trim($_GPC['reward_amount_max']);
            $data['pool_amount'] = trim($_GPC['pool_amount']);
            $data['apiclient_key'] = trim($_GPC['apiclient_key']);
            $data['apiclient_cert'] = trim($_GPC['apiclient_cert']);
            $data['packet_rule'] = trim($_POST['packet_rule']);
            $data['createtime'] = time();

            if(empty($item)){
                pdo_insert('wxz_wzb_red_packet',$data);
            }else{
                pdo_update('wxz_wzb_red_packet',$data,array('rid' => $rid));
            }
            message('提交成功','','success');
        }
		
		include $this->template('redpacketsetting');
	}
	
	public function doWebIsauth(){
		global $_W,$_GPC;
		$id= $_GPC['id'];
		$rid= $_GPC['rid'];
		$single = pdo_fetch("select * from ".tablename('wxz_wzb_comment')." where id = ".$id);
		$is_auth= $_GPC['is_auth'];
		if($is_auth == 1){
			$single['is_auth'] = $is_auth;
			unset($single['id']);
			pdo_insert('wxz_wzb_comment',$single);
			pdo_delete('wxz_wzb_comment', array('id' => $id));
		}else{
			pdo_update('wxz_wzb_comment',array('is_auth'=>$is_auth),array('id' => $single['id']));
		}
		$return_url = $_W['siteroot'] .'web'.str_replace("./","/",$this->createWebUrl('comment',array('rid'=>$rid)));
        header("location: $return_url");
        exit;
	}

	public function doWebLiveList(){
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wxz_wzb_live_setting')." where `uniacid`=:uniacid",array(':uniacid'=>$_W['uniacid']));
		$start = ($pindex - 1) * $psize;

		$sql='SELECT * FROM ' . tablename('wxz_wzb_live_setting') .' WHERE uniacid = '.$_W['uniacid'].' order by id desc limit '.$start.','.$psize;
		
		$list = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('live_list');
	}

	public function doWebWithdraw(){
		global $_W,$_GPC;
		$rid = intval($_GPC['rid']);
		$uid = $_GPC['uid'];
		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );

		$sql='SELECT b.nickname,b.id,b.headimgurl,a.fee,a.dateline,a.status FROM ' . tablename('wxz_wzb_packet_log') . ' as a inner JOIN ' . tablename('wxz_wzb_user') . ' AS b ON a.uid=b.id inner join ' . tablename('wxz_wzb_viewer') . ' as c on b.id=c.uid WHERE a.uniacid = '.$_W['uniacid'].' and a.uid='.$uid.' and c.rid='.$rid.' and a.status!="created" and a.type=2 order by id desc';

		$list = pdo_fetchall($sql);

		include $this->template('withdraw');
	}

	public function doWebDel(){
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		pdo_delete('wxz_wzb_live_setting', array('id' => $id));
		message('删除成功', referer(),'success');
	}

	public function doWebDelpic(){
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		pdo_delete('wxz_wzb_live_pic', array('id' => $id));
		message('删除成功', referer(),'success');
	}

	public function doWebSendGroupPacket(){
		global $_W,$_GPC;
		$rid = intval($_GPC['rid']);
		if(isset($_GPC['item']) && $_GPC['item'] == 'ajax' && $_GPC['key'] == 'setting'){
            $data=array(
                'uniacid'=>$_W['uniacid'],
                'content'=>$_POST['content'],
				'isadmin'=>1,
				'is_auth'=>1,
				'type'=>$_GPC['type'],
				'headimgurl'=>$_GPC['headimgurl'],
				'nickname'=>$_GPC['nickname'],
				'num'=>$_GPC['num'],
				'amount'=>$_GPC['amount'],
				'ispacket'=>1,
				'rid'=>$_GPC['rid'],
				'dateline'=>time()
            );
            pdo_insert('wxz_wzb_comment', $data);
		}

        load()->func('tpl');
		include $this->template('sendGroupPacket');
	}

	public function doWebLivePic(){
		global $_W,$_GPC;
		$rid = intval($_GPC['rid']);
		$id = intval($_GPC['id']);
		if(isset($_GPC['item']) && $_GPC['item'] == 'ajax' && $_GPC['key'] == 'setting'){
            $data=array(
                'uniacid'=>$_W['uniacid'],
                'content'=>$_POST['content'],
                'title'=>$_GPC['title'],
				'rid'=>$_GPC['rid'],
				'dateline'=>time()
            );
            pdo_insert('wxz_wzb_live_pic', $data);
		}
		if($id){
			$item = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_live_pic') . ' WHERE `uniacid` = :uniacid and id=:id', array(':uniacid' => $_W['uniacid'],':id' => $id));
		}
		

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;

		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wxz_wzb_live_pic')." where `uniacid`=:uniacid and rid=:rid",array(':uniacid'=>$_W['uniacid'],':rid' => $rid));
		$start = ($pindex - 1) * $psize;

		$sql='SELECT * FROM ' . tablename('wxz_wzb_live_pic') .' WHERE uniacid = '.$_W['uniacid'].' and rid='.$rid.' order by id desc limit '.$start.','.$psize;
		
		$LivePic = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);

        load()->func('tpl');
		include $this->template('live_pic');
	}

	public function doWebComment(){
		global $_W, $_GPC;
		$rid = intval($_GPC['rid']);
        $pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wxz_wzb_comment')." where `uniacid`=:uniacid and rid=:rid and ispacket!=1 or isadmin=1",array(':uniacid'=>$_W['uniacid'],':rid' => $rid));
		$start = ($pindex - 1) * $psize;

		$sql='SELECT * FROM ' . tablename('wxz_wzb_comment') .' WHERE uniacid = '.$_W['uniacid'].' and rid='.$rid.'  and ispacket!=1 or isadmin=1 order by id desc limit '.$start.','.$psize;
		
		$Comment = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);

		include $this->template('comment');
	}

	public function doWebGetpacket(){
		global $_W, $_GPC;
		$rid = intval($_GPC['rid']);
		$toid = intval($_GPC['toid']);
        $pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wxz_wzb_comment')." where `uniacid`=:uniacid and rid=:rid and toid=:toid",array(':uniacid'=>$_W['uniacid'],':rid' => $rid,':toid' => $toid));
		$start = ($pindex - 1) * $psize;

		$sql='SELECT * FROM ' . tablename('wxz_wzb_comment') .' WHERE uniacid = '.$_W['uniacid'].' and rid='.$rid.' and toid='.$toid.' order by id desc limit '.$start.','.$psize;
		
		$Comment = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);

		include $this->template('getpacket');
	}

	public function doWebUsers(){
		global $_W, $_GPC;
		$rid = intval($_GPC['rid']);
        $pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wxz_wzb_viewer')." as a inner JOIN " . tablename('wxz_wzb_user') . " AS b ON a.uid=b.id where b.`uniacid`=:uniacid and a.rid=:rid",array(':uniacid'=>$_W['uniacid'],':rid' => $rid));
		$start = ($pindex - 1) * $psize;

		$sql='SELECT * FROM ' . tablename('wxz_wzb_viewer').' as a inner JOIN ' . tablename('wxz_wzb_user') . ' AS b ON a.uid=b.id where b.`uniacid`='.$_W['uniacid'].' and a.rid='.$rid.' order by b.id desc limit '.$start.','.$psize;
		
		$Users = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);

		include $this->template('users');
	}
	
	public function doWebHelp(){
		global $_W,$_GPC;
		$rid = intval($_GPC['rid']);
		$uid = $_GPC['uid'];
		$user = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_user') . ' WHERE `uniacid` = :uniacid AND `id` = :uid', array(':uniacid' => $_W['uniacid'],':uid' => $uid) );
		
		$viewer = pdo_fetch('SELECT * FROM ' . tablename('wxz_wzb_viewer') . ' WHERE `uid` = :uid AND `rid` = :rid', array(':uid' => $uid,':rid' => $rid) );

		$sql='SELECT b.nickname,b.id,b.headimgurl,a.amount,a.dateline FROM ' . tablename('wxz_wzb_share') . ' as a LEFT JOIN ' . tablename('wxz_wzb_user') . ' AS b ON a.help_uid=b.id inner join '.tablename('wxz_wzb_viewer').' as c on b.id=c.uid WHERE a.uniacid = '.$_W['uniacid'].' and a.share_uid='.$uid.' and c.rid='.$rid.' order by id desc';
		$help_user = pdo_fetchall($sql);

		include $this->template('help');
	}

	public function checkMobile(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			echo "HTTP/1.1 401 Unauthorized";exit;
		}
	}

}