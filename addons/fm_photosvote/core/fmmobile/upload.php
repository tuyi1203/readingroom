<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

if ($rshare['subscribe'] && !$follow) {
	$fmdata = array(
		"success" => -1,
		"flag" => 5,
		"msg" => '请先关注',
	);
	echo json_encode($fmdata);
	exit();
}
			
$value = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_wm_upload_value')." WHERE tfrom_user = :tfrom_user and from_user = :from_user LIMIT 1", array(':tfrom_user' => $tfrom_user,':from_user' => $from_user));
$filed = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_wm_upload')." WHERE rid = :rid LIMIT 1", array(':rid' => $rid));


if( $_GPC['upload'] == '1' ){
	$fmdata = array(
		"success" => -1,
		"type" => 'error',
		"msg" => '请先关注',
	);
	if( $filed['filed1name'] && $filed['filed1just'] == 1 && $_GPC['filed1val'] == '' ){
		$fmdata['msg'] = $filed['filed1name'].'不能为空！';
	}else if( $filed['filed2name'] && $filed['filed2just'] == 1 && $_GPC['filed2val'] == '' ){
		$fmdata['msg'] = $filed['filed2name'].'不能为空！';
	}else if( $filed['filed3name'] && $filed['filed3just'] == 1 && $_GPC['filed3val'] == '' ){
		$fmdata['msg'] = $filed['filed3name'].'不能为空！';
	}else if( $filed['filed4name'] && $filed['filed4just'] == 1 && $_GPC['filed4val'] == '' ){
		$fmdata['msg'] = $filed['filed4name'].'不能为空！';
	}else if( $filed['filed5name'] && $filed['filed5just'] == 1 && $_GPC['filed5val'] == '' ){
		$fmdata['msg'] = $filed['filed5name'].'不能为空！';
	}else if( $_GPC['pic'] == ''){
		$fmdata['msg'] = '对不起，请上传图片！';
	}else if( $_GPC['tfrom_user'] == '' || $_GPC['rid'] == ''){
		$fmdata['msg'] = '对不起，活动参数错误！';
	}else{
		//查询当前活动的选手是否已经上传过了
		$where['rid'] = $_GPC['rid'];
		$where['tfrom_user'] = $_GPC['tfrom_user'];
		$where['from_user'] = $_GPC['from_user'];
		$count = pdo_getcolumn('fm_photosvote_wm_upload_value', $where, 'id',1);

		if($count>0){
			$fmdata['msg'] = '对不起，您已经参与过了！';
		}else{
			//保存图片
			$harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
			preg_match("/data:image\/(.*?);base64/",$_GPC['pic'],$res);
			$ext = $res[1];
			$setting = $_W['setting']['upload']['image'];
			if (!in_array(strtolower($ext), $setting['extentions']) || in_array(strtolower($ext), $harmtype)) {
				$fmdata = array(
					"msg" => '系统不支持您上传的文件（扩展名为：'.$ext.'）,请上传正确的图片文件',
				);
			}else{
				load()->func('file');
				$photoname = 'FMFetchi'.date('YmdHis').random(16);
				$nfilename = $photoname.'.'.$ext;
				$updir = '../attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/';
				ini_set("display_errors", "On");
				mkdirs($updir);
				$darr = explode("base64,", $_GPC['pic'],30);
				$picData = end($darr);
				if (!$picData) {
					$fmdata = array(
						"msg" => '当前图片宽度大于3264px,系统无法识别为其生成！',
					);
				}else{
					if (file_put_contents($updir.$nfilename,base64_decode($picData))===false) {
						$fmdata = array(
							"msg" => '上传错误',
						);
					}else{
						//缩放图片
						 //因为PHP只能对资源进行操作，所以要对需要进行缩放的图片进行拷贝，创建为新的资源 
						 $src=imagecreatefromjpeg($updir.$nfilename); 
						 //取得源图片的宽度和高度 
						 $size_src=getimagesize($updir.$nfilename); 
						 $w=$size_src['0']; 
						 $h=$size_src['1'];
						 //指定缩放出来的最大的宽度（也有可能是高度） 
						 $max=$filed['piczoom']; 
						 //根据最大值为200，算出另一个边的长度，得到缩放后的图片宽度和高度 
						 if($w > $h){ 
							 $w=$max; 
							 $h=$h*($max/$size_src['0']); 
						 }else{ 
							 $h=$max; 
							 $w=$w*($max/$size_src['1']); 
						 } 
						 //声明一个$w宽，$h高的真彩图片资源 
						 $image=imagecreatetruecolor($w, $h); 
						 //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h） 
						 imagecopyresampled($image, $src, 0, 0, 0, 0, $w, $h, $size_src['0'], $size_src['1']); 
						 //保存图片
						imagejpeg($image, $updir.$nfilename);
						imagedestroy($image);
	
	
						$data['rid'] = $_GPC['rid'];
						$data['tfrom_user'] = $_GPC['tfrom_user'];
						$data['from_user'] = $_GPC['from_user'];
						$data['head'] = $_W['fans']['tag']['avatar'];
						$data['nickname'] = $_W['fans']['tag']['nickname'];
						$data['pic'] = $updir.$nfilename;
						$data['filed1val'] = $_GPC['filed1val'];
						$data['filed2val'] = $_GPC['filed2val'];
						$data['filed3val'] = $_GPC['filed3val'];
						$data['filed4val'] = $_GPC['filed4val'];
						$data['filed5val'] = $_GPC['filed5val'];
						$data['time'] = time();
				
						pdo_insert('fm_photosvote_wm_upload_value', $data);
						
						$fmdata = array(
							"success" => 1,
							"type" => 'success',
							"msg" => '图片上传成功！',
						);
					}
				}
			}
		}

	}
	message($fmdata['msg'],null,$fmdata['type']);
	exit();
	echo json_encode($fmdata);
	exit();
}



$title = $rshare['sharetitle'] . '报名';
$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
 $_share['title'] = $this->get_share($uniacid,$rid,$from_user,$rshare['sharetitle']);
$_share['content'] =  $this->get_share($uniacid,$rid,$from_user,$rshare['sharecontent']);
$_share['imgUrl'] = toimage($rshare['sharephoto']);
if (!empty($rbody)) {
	$rbody_reg = iunserializer($rbody['rbody_reg']);
}
$templatename = $rbasic['templates'];
if ($templatename != 'default' && $templatename != 'stylebase') {
	require FM_CORE. 'fmmobile/tp.php';
}
$toye = $this->templatec($templatename,$_GPC['do']);
include $this->template($toye);

