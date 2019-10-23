<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
$now = time();
//查询自己是否参与活动
if (!empty($from_user)) {
	$mygift = pdo_fetch("SELECT * FROM " . tablename($this -> table_voteer) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));
} else {
	$fmdata = array("success" => -1, "msg" => '获取用户openid失败，请关闭重试', );
	echo json_encode($fmdata);
	exit();
}
if ($_GPC['upphotosone'] == 'start') {
	load()->func('file');
	$base64 = file_get_contents("php://input");
	//获取输入流
	$base64 = json_decode($base64, 1);
	$data = $base64['base64'];

	if ($data) {
		$harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');

		preg_match("/data:image\/(.*?);base64/", $data, $res);
		$ext = $res[1];
		$setting = $_W['setting']['upload']['image'];
		if (!in_array(strtolower($ext), $setting['extentions']) || in_array(strtolower($ext), $harmtype)) {
			$fmdata = array("success" => -1, "msg" => '系统不支持您上传的文件（扩展名为：' . $ext . '）,请上传正确的图片文件', );
			echo json_encode($fmdata);
			die ;
		}

		$photoname = 'FMFetchi' . date('YmdHis') . random(16);
		$nfilename = $photoname . '.' . $ext;
		$updir = '../attachment/images/' . $uniacid . '/' . date("Y") . '/' . date("m") . '/';
		mkdirs($updir);

		//$data = preg_replace("/^data:image\/(.*);base64,/","",$data);
		$darr = explode("base64,", $data, 30);
		$data = end($darr);
		if (!$data) {
			$fmdata = array("success" => -1, "msg" => $data . '当前图片宽度大于3264px,系统无法识别为其生成！', );
			echo json_encode($fmdata);
			exit ;
		}

		if (file_put_contents($updir . $nfilename, base64_decode($data)) === false) {
			$fmdata = array("success" => -1, "msg" => '上传错误', );
			echo json_encode($fmdata);
			exit ;
		} else {
			$mid = $_GPC['mid'];
			$avatar = pdo_fetch("SELECT avatar FROM " . tablename($this -> table_voteer) . " WHERE rid = :rid AND from_user =:from_user LIMIT 1", array(':rid' => $rid, ':from_user' => $from_user));

			//if (!$qiniu['isqiniu']) {
				$picurl = $updir . $nfilename;

				file_delete($avatar['avatar']);
				//file_delete($updir.$nfilename);
				$insertdata = array('lasttime' => $now, "avatar" => $picurl, );
				pdo_update($this -> table_voteer, $insertdata, array('rid' => $rid, 'from_user' => $from_user));

				$fmdata = array("success" => 1, "msg" => '上传成功！', "imgurl" => $picurl, );
				echo json_encode($fmdata);
				exit();
			//}
		}

	} else {
		$fmdata = array("success" => -1, "msg" => '没有发现上传图片', );
		echo json_encode($fmdata);
		exit();
	}
} else {
	$name = $_GPC['value_name'];
	$value = $_GPC['value_val'];
	if (!empty($value)) {
		if ($name == 'realname') {
			if ($mygift['realname']) {
				if ($mygift['realname'] == $value) {
					$fmdata = array("success" => 1, "flag" => 1);
					echo json_encode($fmdata);
					exit();
				} else {
					$realname = pdo_fetch("SELECT * FROM " . tablename($this -> table_voteer) . " WHERE realname = :realname and rid = :rid", array(':realname' => $value, ':rid' => $rid));
					if (!empty($realname)) {
						$msg = '该姓名已经存在，请重新填写！';
						$fmdata = array("success" => -1, "flag" => 2, "msg" => $msg);
						echo json_encode($fmdata);
						exit();
					}
				}

			}

		}
		if ($name == 'mobile') {
			if (!preg_match(REGULAR_MOBILE, $value)) {
				$msg = '手机号格式为 11 位数字。';
				$fmdata = array("success" => -1, "flag" => 2, "msg" => $msg, );
				echo json_encode($fmdata);
				exit();
			}
			if ($mygift['mobile']) {
				if ($mygift['mobile'] == $value) {
					$fmdata = array("success" => 1, "flag" => 1);
					echo json_encode($fmdata);
					exit();
				} else {
					$ymobile = pdo_fetch("SELECT * FROM " . tablename($this -> table_voteer) . " WHERE mobile = :mobile and rid = :rid", array(':mobile' => $value, ':rid' => $rid));
					if (!empty($ymobile)) {
						$msg = '非常抱歉，此手机号码已经被注册，你需要更换注册手机号！';
						$fmdata = array("success" => -1, "flag" => 2, "msg" => $msg, );
						echo json_encode($fmdata);
						exit();
					}
				}
			}
		}

		$data = array();
		if ($name == 'sexa') {
			$data['sex'] = 1;
		} elseif ($name == 'sexb') {
			$data['sex'] = 2;
		} else {
			$data[$name] = $value;
		}
		pdo_update($this -> table_voteer, $data, array('rid' => $rid, 'from_user' => $from_user));
		$fmdata = array("success" => 1, "msg" => '自动保存成功', );
		echo json_encode($fmdata);
		exit();
	} else {
		$fmdata = array("success" => -1, "flag" => 1, "msg" => '不能为空', );

		echo json_encode($fmdata);
		exit();
	}
}
