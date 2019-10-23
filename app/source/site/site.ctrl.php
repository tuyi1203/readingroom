<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
// error_reporting(E_ALL);
$do = in_array($do, array('list', 'detail', 'handsel', 'message','watercharge_default','watercharge_search','waterinvoice_default','waterinvoice_do','watercharge_getinvoice','rmquest_default','rmquest_do')) ? $do : 'list';
load()->model('site');
load()->model('mc');
if($do == 'list')
{
	$cid = intval($_GPC['cid']);
	$category = pdo_fetch("SELECT * FROM ".tablename('site_category')." WHERE id = '{$cid}' AND uniacid = '{$_W['uniacid']}'");
	if (empty($category)) {
		message('分类不存在或是已经被删除！');
	}
	if (!empty($category['linkurl'])) {
		header('Location: '.$category['linkurl']);
		exit;
	}
	$_share['desc'] = $category['description'];
	$_share['title'] = $category['name'];

	$title = $category['name'];
	$category['template'] = pdo_fetchcolumn('SELECT b.name FROM ' . tablename('site_styles') . ' AS a LEFT JOIN ' . tablename('site_templates') . ' AS b ON a.templateid = b.id WHERE a.id = :id', array(':id' => $category['styleid']));
	if(!empty($category['template'])) 
	{
		$styles_vars = pdo_fetchall('SELECT * FROM ' . tablename('site_styles_vars') . ' WHERE styleid = :styleid', array(':styleid' => $category['styleid']));
		if(!empty($styles_vars))
		{
			foreach($styles_vars as $row) {
				if (strexists($row['variable'], 'img')) {
					$row['content'] = tomedia($row['content']);
				}
				$_W['styles'][$row['variable']] = $row['content'];
			}
		}
	}

	//2018.11.6 jimmyTu 取得文章一览
	$article_p =  pdo_fetchall("SELECT * FROM ".tablename('site_article')." WHERE uniacid = '{$_W['uniacid']}' AND pcate = '$cid' and ccate=0 ORDER BY displayorder DESC,id DESC");
	$article_c = pdo_fetchall("SELECT * FROM ".tablename('site_article')." WHERE uniacid = '{$_W['uniacid']}' AND ccate = '$cid' and subchildid is null ORDER BY displayorder DESC,id DESC");
	$article_s = pdo_fetchall("SELECT * FROM ".tablename('site_article')." WHERE uniacid = '{$_W['uniacid']}' AND subchildid = '$cid' ORDER BY displayorder DESC,id DESC");
	$articles = array_merge($article_p,$article_c,$article_s);
	foreach ($articles as $key => $row) 
	{
		if (empty($row['linkurl']))
		{
			$articles[$key]['linkurl'] = url('site/site/detail',array('id'=>$row['id'],'i'=>$row['uniacid']));
		}
	}
	// print_r($articles);


	if (empty($category['ishomepage'])) {
		$ishomepage = 0;
		if(!empty($category['template'])) {
			$_W['template'] = $category['template'];
		}
		if ($category['styleid'] != '0')
		{
			template('site/list'.'_'.$category['styleid']);
		} else {
			template('site/list');
		}
		exit;
	} else {
		if(!empty($category['template'])) {
			$_W['template'] = $category['template'];
		}
		$ishomepage = 1;
				$navs = pdo_fetchall("SELECT * FROM ".tablename('site_category')." WHERE uniacid = '{$_W['uniacid']}' AND parentid = '$cid' ORDER BY displayorder DESC,id DESC");
		if (!empty($navs)) {
			foreach ($navs as &$row) {
				if(empty($row['linkurl']) || (!strexists($row['linkurl'], 'http://') && !strexists($row['linkurl'], 'https://'))) {
					$row['url'] = url('site/site/list', array('cid' => $row['id']));
				} else {
					$row['url'] = $row['linkurl'];
				}
				if (!empty($row['icontype']) && $row['icontype'] == 1) {
					$row['css'] = iunserializer($row['css']);
					$row['icon'] = '';
					$row['css']['icon']['style'] = "color:{$row['css']['icon']['color']};font-size:{$row['css']['icon']['font-size']}px;";
					$row['css']['name'] = "color:{$row['css']['name']['color']};";
				}
				if (!empty($row['icontype']) && $row['icontype'] == 2) {
					$row['css'] = '';
				}
			}
		}
		template('home/home');
		exit;
	}
} elseif($do == 'detail') {
	$id = intval($_GPC['id']);
	$sql = "SELECT * FROM " . tablename('site_article') . " WHERE `id`=:id AND uniacid = :uniacid";
	$detail = pdo_fetch($sql, array(':id'=>$id, ':uniacid' => $_W['uniacid']));
	//2018.11.6 jimmyTu 取得cateory名称
	if (!empty($detail['pcate']))
	{
		$cid = $detail['pcate'];
	}
	if (!empty($detail['ccate']))
	{
		$cid = $detail['ccate'];
	}
	if (!empty($detail['subchildid']))
	{
		$cid = $detail['subchildid'];
	}
	$sql = "SELECT name FROM " . tablename('site_category') . "where uniacid=:uniacid and id=:cid";
	$detail['categoryname'] = pdo_fetchcolumn($sql , array(':uniacid'=>$detail['uniacid'],':cid'=>$cid));

	if (!empty($detail['linkurl'])) {
		if(strtolower(substr($detail['linkurl'], 0, 4)) != 'tel:' && !strexists($detail['linkurl'], 'http://') && !strexists($detail['linkurl'], 'https://')) {
			$detail['linkurl'] = $_W['siteroot'] . 'app/' . $detail['linkurl'];
		}
		header('Location: '. $detail['linkurl']);
		exit;
	}
	$detail = istripslashes($detail);

	$detail['content']= preg_replace("/<img(.*?)(http[s]?\:\/\/mmbiz.qpic.cn[^\?]*?)(\?[^\"]*?)?\"/i", '<img $1$2"', $detail['content']);

	if (!empty($detail['incontent'])) {
		$detail['content'] = '<p><img src="' . tomedia($detail['thumb']) . '" title="' . $detail['title'] . '" /></p>' . $detail['content'];
	}
	if(!empty($detail['thumb'])) {
		$detail['thumb'] = tomedia($detail['thumb']);
	} else {
		$detail['thumb'] = '';
	}
	$title = $detail['title'];
		if(!empty($detail['template'])) {
		$_W['template'] = $detail['template'];
	}
	
	if($_W['os'] == 'android' && $_W['container'] == 'wechat' && $_W['account']['account']) {
		$subscribeurl = "weixin://profile/{$_W['account']['account']}";
	} else {
		$sql = 'SELECT `subscribeurl` FROM ' . tablename('account_wechats') . " WHERE `acid` = :acid";
		$subscribeurl = pdo_fetchcolumn($sql, array(':acid' => intval($_W['acid'])));
	}
		$detail['click'] = intval($detail['click']) + 1;
	pdo_update('site_article', array('click' => $detail['click']), array('uniacid' => $_W['uniacid'], 'id' => $id));
		$_share = array('desc' => $detail['description'], 'title' => $detail['title'], 'imgUrl' => $detail['thumb']);

		if ($detail['categoryname'] == '停水公告' && (substr($detail['title'] , 0 , 8) < date("Ymd" , $detail['createtime']))) 
	{ 
		$detail['time'] =  date('Y-m-d',strtotime(substr($detail['title'] , 0 , 8 ))); 
		// echo date('Y-m-d' , strtotime(substr($detail['title'] , 0 , 8 )));
	}
	else
	{
		$detail['time'] = date('Y-m-d' , $detail['createtime']);
	}
	
	template('site/detail');

	
	

} elseif($do == 'handsel') {
		if($_W['ispost']) {
		$id = intval($_GPC['id']);
		$article = pdo_fetch('SELECT id, credit FROM ' . tablename('site_article') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		$credit = iunserializer($article['credit']) ? iunserializer($article['credit']) : array();
		if(!empty($article) && $credit['status'] == 1) {
			if($_GPC['action'] == 'share') {
				$touid = $_W['member']['uid'];
				$formuid = -1;
				$handsel = array('module' => 'article', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'share', 'credit_value' => $credit['share'], 'credit_log' => '分享文章,赠送积分');
			} elseif($_GPC['action'] == 'click') {
				$touid = intval($_GPC['u']);
				$formuid = CLIENT_IP;
				$handsel = array('module' => 'article', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'click', 'credit_value' => $credit['click'], 'credit_log' => '分享的文章在朋友圈被阅读,赠送积分');
			}
			$total = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign', array(':uniacid' => $_W['uniacid'], ':module' => 'article', ':sign' => $handsel['sign']));
			if(($total >= $credit['limit']) || (($total + $handsel['credit_value']) > $credit['limit'])) {
				exit(json_encode(error(-1, '赠送积分已达到上限')));
			}

			$status = mc_handsel($touid, $formuid, $handsel, $_W['uniacid']);
			if(is_error($status)) {
				exit(json_encode($status));
			} else {
				exit('success');
			}
		} else {
			exit(json_encode(array(-1, '文章没有设置赠送积分')));
		} 
	} else {
		exit(json_encode(array(-1, '非法操作')));
	}
}
elseif ($do == 'message')
{
	template('site/message');
	exit;
}
elseif ($do == 'rmquest_default')
{
	// mc_oauth_userinfo();
	$title = '报名登记表';
	template('site/quest');
	exit;
}
elseif ($do == 'rmquest_do')
{
	if (empty($_W['fans']['nickname'])) {
		mc_oauth_userinfo();
	}
	$insertData = [
		'uniacid' => $_W['uniacid'],
		'avatar' => $_W['fans']['tag']['avatar'],
		'nickname' =>$_W['fans']['tag']['nickname'],
		'openid' => $_W['openid'],
		'student_name' => $_GPC['name'],
		'gender' => intval($_GPC['gender']),
		'birthday' => $_GPC['birthday'],
		'youeryuan' => $_GPC['youeryuan'],
		'addr' => $_GPC['addr'],
		'p1_name' => $_GPC['p1_name'],
		'p1_relation' => $_GPC['p1_relation'],
		'p1_xueli' => $_GPC['p1_xueli'],
		'p1_biye' => $_GPC['p1_biye'],
		'p1_tel' => $_GPC['p1_tel'],
		'p1_danwei' => $_GPC['p1_danwei'],
		'p2_name' => $_GPC['p2_name'],
		'p2_relation' => $_GPC['p2_relation'],
		'p2_xueli' => $_GPC['p2_xueli'],
		'p2_biye' => $_GPC['p2_biye'],
		'p2_tel' => $_GPC['p2_tel'],
		'p2_danwei' => $_GPC['p2_danwei'],
		'createtime' => date("Y-m-d H:i:s"),
	];
	//数据验证
	$errmsg = checkForm($insertData);
	if (count($errmsg) > 0)
	{
		$return = ['result' => 'fail' , 'msg'=>$errmsg];
	} else {
    //当月只能提交一次
		$createtime = date('Y-m') . '%';
		$sent = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jm_questionaire') . " WHERE openid= :openid and createtime like :createtime", [':openid'=>$insertData['openid'], ':createtime'=>$createtime]);
		if ($sent > 0) {
			$return = ['result' => 'fail' , 'msg'=>'您本月已经报名，感谢您的支持！'];
		} else {
      $result = pdo_insert('jm_questionaire', $insertData);
      if (!empty($result)) {
        $return = ['result' => 'success' , 'msg'=>'提交成功....'];
      } else {
        $return = ['result' => 'fail' , 'msg'=>'服务器忙碌，请稍后再试....'];
      }
    }
	}
	echo json_encode($return);
}

function checkForm($input)
{
	require_once dirname(__FILE__).'/validate.php';
	$msg = [];
	$validater = new validate();

	if (empty($input['student_name'])) 
	{
		$msg[]  = "请输入学生姓名";
	}
	
	if (empty($input['gender'])) 
	{
		$msg[]  = "请输入性别";
	}

	if (empty($input['birthday'])) 
	{
		$msg[]  = "请输入生日";
	}

	if (empty($input['youeryuan'])) 
	{
		$msg[]  = "请输入幼儿园";
	}

	if (empty($input['addr'])) 
	{
		$msg[]  = "请输入地址";
	}

	if (empty($input['p1_name'])) 
	{
		$msg[]  = "请输入家长名字";
	}

	if (empty($input['p1_name'])) 
	{
		$msg[]  = "请输入家长名字";
	}

	if (empty($input['p1_relation'])) 
	{
		$msg[]  = "请输入与学生关系";
	}

	if (empty($input['p1_biye'])) 
	{
		$msg[]  = "请输入毕业院校";
	}

	if (empty($input['p1_tel'])) 
	{
		$msg[]  = "请输入电话号码";
	}
	else if (!($validater->valid_mobile($input['p1_tel'])))
	{
		$msg[] = '请输入正确的电话号码';
	}

	if (empty($input['p1_danwei'])) 
	{
		$msg[]  = "请工作输入单位";
	}

	return $msg;
}