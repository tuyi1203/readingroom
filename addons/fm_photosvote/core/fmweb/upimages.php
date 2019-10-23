<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
		$from_user = $_GPC['from_user'];//
		$rbasic = pdo_fetch('SELECT * FROM '.tablename($this->table_reply).' WHERE rid =:rid ', array(':rid' => $rid) );
		$rvote = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$dreply = pdo_fetch("SELECT csrs_total FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$reply = array_merge($rbasic, $rvote, $dreply);
		$qiniu = iunserializer($reply['qiniu']);
		$now = time();
		load()->func('file');
		if(!empty($from_user)) {
			$mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user,':rid' => $rid));
		}
		$uid = pdo_fetch("SELECT uid FROM ".tablename($this->table_users)." WHERE rid = :rid ORDER BY uid DESC, id DESC LIMIT 1", array(':rid' => $rid));
		if (empty($mygift)) {
			$insertdata = array(
				'rid'       => $rid,
				'uid'       => $uid['uid'] + 1,
				'uniacid'      => $uniacid,
				'from_user' => $from_user,
				'avatar'    => $avatar,
				'nickname'  => $nickname,
				'sex'  => $sex,
				'photo'  => '',
				'description'  => '',
				'photoname'  => '',
				'realname'  => '',
				'mobile'  => '',
				'weixin'  => '',
				'qqhao'  => '',
				'email'  => '',
				'job'  => '',
				'xingqu'  => '',
				'address'  => '',
				'photosnum'  => '0',
				'xnphotosnum'  => '0',
				'hits'  => '1',
				'xnhits'  => '1',
				'yaoqingnum'  => '0',
				'createip' => getip(),
				'lastip' => getip(),
				'status'  => '2',
				'sharetime' => $now,
				'createtime'  => $now,
			);
			$insertdata['iparr'] = getiparr($insertdata['lastip']);

			pdo_insert($this->table_users, $insertdata);
			pdo_update($this->table_reply_display, array('csrs_total' => $reply['csrs_total']+1), array('rid' => $rid));

		   if($reply['isfans']){
				if($myavatar){
					fans_update($from_user, array(
						'avatar' => $myavatar,
					));
				}
				if($mynickname){
					fans_update($from_user, array(
						'nickname' => $mynickname,
					));
				}

				if($reply['isrealname']){
					fans_update($from_user, array(
						'realname' => $realname,
					));
				}
				if($reply['ismobile']){
					fans_update($from_user, array(
						'mobile' => $mobile,
					));
				}
				if($reply['isqqhao']){
					fans_update($from_user, array(
						'qq' => $qqhao,
					));
				}
				if($reply['isemail']){
					fans_update($from_user, array(
						'email' => $email,
					));
				}
				if($reply['isaddress']){
					fans_update($from_user, array(
						'address' => $address,
					));
				}
			}
		}
		if ($_GPC['upphotosone'] == 'start') {
			$base64=file_get_contents("php://input"); //获取输入流
			$base64=json_decode($base64,1);
			$data = $base64['base64'];

			if($data){
				$harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');

				preg_match("/data:image\/(.*?);base64/",$data,$res);
				$ext = $res[1];
				$setting = $_W['setting']['upload']['image'];
				if (!in_array(strtolower($ext), $setting['extentions']) || in_array(strtolower($ext), $harmtype)) {
					$fmdata = array(
						"success" => -1,
						"msg" => '系统不支持您上传的文件（扩展名为：'.$ext.'）,请上传正确的图片文件',
					);
					echo json_encode($fmdata);
					die;
				}

				$photoname = 'FMFetchi'.date('YmdHis').random(16);
				$nfilename = $photoname.'.'.$ext;
				$updir = '../attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/';
				mkdirs($updir);
				//$data = preg_replace("/data:image\/(.*);base64,/","",$data);
				$darr = explode("base64,", $data,30);
				$data = end($darr);
				if (file_put_contents($updir.$nfilename,base64_decode($data))===false) {
					$fmdata = array(
						"success" => -1,
						"msg" => '上传错误',
					);
					echo json_encode($fmdata);
				}else{
					$mid = $_GPC['mid'];
					$photosarrnum = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_users_picarr)." WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user,':rid' => $rid));
					$username = pdo_fetch("SELECT photoname FROM ".tablename($this->table_users_picarr)." WHERE rid = :rid AND id =:id LIMIT 1", array(':rid' => $rid,':id' => $mid));

					if (!$qiniu['isqiniu']) {
						$picurl = $updir.$nfilename;
						if (!empty($username['photoname'])) {
							file_delete($updir.$username['photoname']);
							file_delete($updir.$nfilename);
							$insertdata = array(
								'photoname' => $photoname,
								'createtime' => $now,
								'mid' => $mid,
								'photos' => $picurl,
								'imgpath' => $picurl,
							);
							pdo_update($this->table_users_picarr, $insertdata, array('rid' => $rid,'from_user' => $from_user, 'id'=>$mid));
							$lastmid = $mid;
						}else{
							if ($photosarrnum >= $reply['tpxz']) {
								$fmdata = array(
									"success" => -1,
									"msg" => '抱歉，你只能上传 '.$reply['tpxz'].' 张图片。',
								);
								echo json_encode($fmdata);
								exit;
							}
							$insertdata = array(
								'rid'       => $rid,
								'uniacid'      => $uniacid,
								'photoname' => $photoname,
								'from_user' => $from_user,
								'status' => 1,
								'imgpath' => $picurl,
								'createtime' => $now,
							);


							if ($photosarrnum < 1) {
								$insertdata['isfm'] = 1;
							}
							$insertdata['photos'] = $picurl;
							pdo_insert($this->table_users_picarr, $insertdata);
							$lastmid = pdo_insertid();
							pdo_update($this->table_users_picarr, array('mid' => $lastmid), array('rid' => $rid,'from_user' => $from_user, 'id'=>$lastmid));
						}

						$addlastmid = $lastmid + 1;
						$photosarrnum = $photosarrnum + 1;

						$fmdata = array(
							"success" => 1,
							"lastmid" => $lastmid,
							"addlastmid" => $addlastmid,
							"photosarrnum" => $photosarrnum,
							"msg" => '上传成功！',
							"imgurl" => $picurl,
						);
						echo json_encode($fmdata);
						exit();
					}else {
						$qiniu['upurl'] = $_W['siteroot'].'attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/'.$nfilename;
						$picurl = $updir.$nfilename;
						$username['type'] = '3';
						$qiniuimages = $this->fmqnimages($nfilename, $qiniu, $mid, $username);
						if ($qiniuimages['success'] == '-1') {
							$fmdata = array(
								"success" => -1,
								"msg" => $qiniuimages['msg'],
							);
							echo json_encode($fmdata);
							exit();
						}else {
							if (!empty($username['photoname'])) {

								file_delete($updir.$username['photoname']);
								//file_delete($updir.$nfilename);
								$insertdata = array(
									'photoname' => $photoname,
									'createtime' => $now,
									"mid" => $mid,
									'imgpath' => $picurl,
									"photos" => $qiniuimages['picarr_'.$mid],
								);
								pdo_update($this->table_users_picarr, $insertdata, array('rid' => $rid,'from_user' => $from_user, 'id' => $mid));
								$lastmid = $mid;
							}else{
								if ($photosarrnum >= $reply['tpxz']) {
									$fmdata = array(
										"success" => -1,
										"msg" => '抱歉，你只能上传 '.$reply['tpxz'].' 张图片。',
									);
									echo json_encode($fmdata);
									exit;
								}
								$insertdata = array(
									'rid'       => $rid,
									'uniacid'      => $uniacid,
									'from_user' => $from_user,
									'photoname' => $photoname,
									'photos' => $qiniuimages['picarr_'.$mid],
									'imgpath' => $picurl,
									'status' => 1,
									'createtime' => $now,
								);
								if ($photosarrnum < 1) {
									$insertdata['isfm'] = 1;
								}
								pdo_insert($this->table_users_picarr, $insertdata);
								//更新mid
								$lastmid = pdo_insertid();
								pdo_update($this->table_users_picarr, array('mid' => $lastmid), array('rid' => $rid,'from_user' => $from_user, 'id'=>$lastmid));

								//file_delete($updir.$nfilename);
							}
							$addlastmid = $lastmid + 1;
							$photosarrnum = $photosarrnum + 1;

							$fmdata = array(
								"success" => 1,
								"lastmid" => $lastmid,
								"addlastmid" => $addlastmid,
								"photosarrnum" => $photosarrnum,
								"msg" => $qiniuimages['msg'],
								"imgurl" => $insertdata['photos'],
							);
							echo json_encode($fmdata);
							exit();
						}

					}
				}

			}else{
				$fmdata = array(
					"success" => -1,
					"msg" =>'没有发现上传图片',
				);
				echo json_encode($fmdata);
				exit();
			}
		}
