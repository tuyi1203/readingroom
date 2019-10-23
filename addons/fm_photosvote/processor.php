<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
require IA_ROOT . '/addons/fm_photosvote/core/defines.php';
require FM_CORE . 'function/processor.php';

class fm_photosvoteModuleProcessor extends WeModuleProcessor {
	public $title 			 = '女神来了！';
	public $table_reply  	 = 'fm_photosvote_reply';//规则 相关设置
	public $table_reply_share  = 'fm_photosvote_reply_share';//规则 相关设置
	public $table_reply_huihua  = 'fm_photosvote_reply_huihua';//规则 相关设置
	public $table_reply_display  = 'fm_photosvote_reply_display';//规则 相关设置
	public $table_reply_vote  = 'fm_photosvote_reply_vote';//规则 相关设置
	public $table_reply_body  = 'fm_photosvote_reply_body';//规则 相关设置
	public $table_users  	 = 'fm_photosvote_provevote';	//报名参加活动的人
	public $table_pnametag 	 = 'fm_photosvote_pnametag';	//默认口号
	public $table_voteer  	 = 'fm_photosvote_voteer';	//投票的人资料
	public $table_tags  	 = 'fm_photosvote_tags';	//
	public $table_users_picarr  = 'fm_photosvote_provevote_picarr';	//
	public $table_users_voice  	= 'fm_photosvote_provevote_voice';	//
	public $table_users_name  	= 'fm_photosvote_provevote_name';	//
	
	public $table_log        = 'fm_photosvote_votelog';//投票记录
	public $table_log_select ='fm_photosvote_votelog1';
	public $table_qunfa        = 'fm_photosvote_qunfa';//投票记录
	public $table_shuapiao        = 'fm_photosvote_vote_shuapiao';//封禁记录
	public $table_shuapiaolog        = 'fm_photosvote_vote_shuapiaolog';//刷票记录
	public $table_bbsreply   = 'fm_photosvote_bbsreply';//投票记录
	public $table_banners    = 'fm_photosvote_banners';//幻灯片
	public $table_advs  	 = 'fm_photosvote_advs';//广告
	public $table_gift  	 = 'fm_photosvote_gift';
	public $table_data  	 = 'fm_photosvote_data';
	public $table_iplist 	 = 'fm_photosvote_iplist';//禁止ip段
	public $table_iplistlog  = 'fm_photosvote_iplistlog';//禁止ip段
	public $table_announce   = 'fm_photosvote_announce';//公告
	public $table_templates   = 'fm_photosvote_templates';//模板
	public $table_designer   = 'fm_photosvote_templates_designer';//模板页面
	public $table_designer_menu   = 'fm_photosvote_templates_designer_menu';//模板页面
	public $table_order   = 'fm_photosvote_order';//付费投票
	public $table_jifen   = 'fm_photosvote_jifen';//积分抽奖
	public $table_jifen_gift   = 'fm_photosvote_jifen_gift';//礼物
	public $table_user_gift   = 'fm_photosvote_user_gift';//礼物
	public $table_user_zsgift   = 'fm_photosvote_user_zsgift';//礼物
	public $table_msg   = 'fm_photosvote_message';//消息
	public $table_orderlog   = 'fm_photosvote_orderlog';//消息
	public $table_counter   = 'fm_photosvote_counter';//投票数据统计
	public $table_qrcode   = 'fm_photosvote_qrcode';//投票二维码

	public function isNeedInitContext() {
		return 0;
	}

	public function respond() {
		global $_W;
		$rid = $this->rule;
		$from_user= $this->message['from'];
		//return $this->respText($this->message);

		if ($this->message['event'] == 'SCAN') {
			if (!empty($this->message['ticket']) && empty($_SESSION['ok'])) {
				$qrcode = pdo_fetch("SELECT keyword FROM ".tablename($this->table_qrcode)." WHERE ticket = :ticket AND rid = :rid", array(':ticket' => $this->message['ticket'],':rid' => $rid));
				$this->message['content'] = $qrcode['keyword'];
			}
		}
		//return $this->respText(iserializer($this->message));

		$tag = $this->message['content'];

		$uniacid = $_W['uniacid'];//当前公众号ID
		load()->func('communication');
			$rbasic = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$rhuihua = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_huihua)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$rvote = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$rdisplay = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			//$row = array_merge($rbasic, $rhuihua, $rvote);

				if (empty($rbasic)) {
					if (!$this->inContext && ($this->message['type'] == 'image' || $this->message['type'] == 'voice' || $this->message['type'] == 'video')) {
						$message = "请按活动规则参与活动，谢谢您的支持！";
					}else {
						$message = "亲，您还没有设置完成关键字或者未添加活动！";
					}

					return $this->respText($message);
				}elseif (empty($rvote)) {
					$message = "亲，您还没有添加设置投票基本设置";
					return $this->respText($message);

				}
			$qiniu = iunserializer($rbasic['qiniu']);
			if ($rbasic['status']==0){
				$message = "亲，".$rbasic['title']."活动暂停了！您可以\n";
				$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&rid=".$rid."&m=fm_photosvote&do=paihang'>看看排行榜</a>\n";
				if (($rhuihua['ishuodong'] == 1 || $rhuihua['ishuodong'] == 3) && !empty($rhuihua['huodongurl'])) {
					$message .= "2、<a href='".$rhuihua['huodongurl']."'>".$rhuihua['huodongname']."</a>";
				}
				return $this->respText($message);
			}
				$now = time();

				if($now >= $rbasic['start_time'] && $now <= $rbasic['end_time']){
					if ($rbasic['status']==0){
						$message = "亲，".$rbasic['title']."活动暂停了！";
						return $this->respText($message);
					}else{


						$command = $rhuihua['command'];
						$tcommand = $rhuihua['tcommand'];
						$rets = preg_match('/'.$tcommand.'/i', $tag);
						if ($tag == '1431' || ($this->message['event'] == 'SCAN' && !$rets)) {
							$picture = toimage($rbasic['picture']);

							return $this->respNews(array(
								'Title' => $rbasic['title'],
								'Description' => htmlspecialchars_decode($rbasic['description']),
								'PicUrl' => $picture,
								'Url' => $this->createMobileUrl('photosvote', array('rid' => $rid,'from_user'=>$from_user)),
							));
						}
						if (empty($command) || $rets) {
							$zjrets = preg_match('/^[0-9]{1,5}$/i', $tag);

							if ($_SESSION['ok'] <> 1  && $this->message['event'] <> 'SCAN') {
								if (!$zjrets && !$rets && !is_numeric($this->message['content'])) {
									$picture = toimage($rbasic['picture']);

									return $this->respNews(array(
										'Title' => $rbasic['title'],
										'Description' => htmlspecialchars_decode($rbasic['description']),
										'PicUrl' => $picture,
										'Url' => $this->createMobileUrl('photosvote', array('rid' => $rid,'from_user'=>$from_user)),
									));
								}


								$this->beginContext(60);//锁定60秒
								$_SESSION['ok']= 1;
								$_SESSION['content']= $this->message['content'];
								$_SESSION['code']=random(4,true);
								return $this->respText("为防止恶意刷票，请回复验证码：".$_SESSION["code"]);

							}else {
								if($this->message['content']!=$_SESSION['code']  && $this->message['event'] <> 'SCAN'){
									$_SESSION['code']=random(4,true);
									return $this->respText("验证码错误，请重新回复验证码：".$_SESSION['code']);
								}else{


									//$ckrets = preg_match('/'.$ckcommand.'/i', $tag);
									if ($this->message['event'] <> 'SCAN') {
										$tag = $_SESSION['content'];
										$this->endContext();
									}else{
										$tag = $this->message['content'];
									}

									$rets = preg_match('/'.$tcommand.'/i', $tag);
									$zjrets = preg_match('/^[0-9]{1,5}$/i', $tag);
									if ($zjrets)  {
										$tp = $this->tp($rid, $tag, $from_user, $uniacid,$rvote,$rdisplay, $rhuihua,$rbasic);
										if (is_array($tp)) {
											return $this->respNews($tp);
										}else{
											return $this->respText($tp);
										}
									}//end zjrets

									if ($rets)  {
										$isret = preg_match('/^'.$tcommand.'/i', $tag);

										if (!$isret) {
											$message = '请输入合适的格式, "'.$tcommand.'+参赛者编号(ID) 或者 参赛者真实姓名", 如: "'.$tcommand.'186 或者 '.$tcommand.'菲儿"';
											return $this->respText($message);
										}
										$ret = preg_match('/(?:'.$tcommand.')(.*)/i', $tag, $matchs);
										$word = $matchs[1];
										$tp = $this->tp($rid, $word, $from_user, $uniacid,$rvote,$rdisplay, $rhuihua,$rbasic);
										if (is_array($tp)) {
											return $this->respNews($tp);
										}else{
											return $this->respText($tp);
										}

									}//$rets

								}
							}
						}else {
							switch ($this->message['type']) {
								case 'text':
									if ($this->message['content'] == $command) {//报名判断
										if (empty($_SESSION['bmstart'])) {
											$isuser = pdo_fetch("SELECT * FROM ".tablename($this->table_users) . " WHERE uniacid = '{$uniacid}' AND `from_user` = '{$from_user}' AND rid = '{$rid}' ");
											if (!empty($isuser)) {
												if ($rdisplay['isindex'] == 1) {
													$advs = pdo_fetch("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY id ASC LIMIT 1");
												}else {
													$advs = array();
													$advs['advname'] = $isuser['description'];
													$advs['thumb'] = $isuser['photo'];
												}
												return $this->respNews(array(
													'Title' => '您已经报过名了，点击以完善信息',
													'Description' => $advs['advname'],
													'PicUrl' => toimage($advs['thumb']),
													'Url' => $this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $from_user))
												));
											}
											$this->beginContext(1800);

											$_SESSION['bmstart']= $this->message['content'];
											//$_SESSION['mediaid']= $this->message['mediaid'];
											//$_SESSION['ok']= 1;
											$msg = "欢迎参加".$rbasic['title']."的活动，现在开始报名\n\n"."请按下面的顺序报名：\n";
											$msg.= "▶️ 上传相册照片\n";
											if ($rvote['mediatypem']) {
												$msg.= "▶️ 录制好声音\n";
											}
											if ($rvote['mediatypev']) {
												$msg.= "▶️ 录制视频\n";
											}
											$msg.= "▶️ 根据提示，填写报名资料\n\n";
											$msg.= $_W['account']['name']."感谢您的参与!\n";

											return $this->respText($msg);

										}else {
											$_SESSION['bmstart']= $this->message['content'];
											$msg = "帮助信息：\n--------------\n\n"."请按下面的顺序报名：\n";
												$msg.= "▶️ 上传相册照片\n";
											if ($rvote['mediatypem']) {
												$msg.= "▶️ 录制好声音\n";
											}
											if ($rvote['mediatypev']) {
												$msg.= "▶️ 录制视频\n";
											}
											$msg.= "▶️ 根据提示，填写报名资料\n\n";
											$msg.= $_W['account']['name']."感谢您的参与!\n";

											return $this->respText($msg);
										}
									}else {
										if ($this->inContext && !empty($_SESSION['bmstart'])) {
											if ($this->message['content'] == 'tc') {
												$this->endContext();
												return $this->respText('退出成功！');
											}

											if ($rvote['mediatype']) {
												if ($_SESSION['imagesok'] <> 1) {
													$msg = $_W['account']['name']." 提请您：\n▶️ 请上传相册照片\n";
													return $this->respText($msg);
												}
											}

											if ($rvote['mediatypem']) {
												if ($_SESSION['voiceok'] <> 1) {
													$msg = $_W['account']['name']." 提请您：\n▶️ 请录制好声音\n";
													return $this->respText($msg);
												}
											}

											if ($rvote['mediatypev']) {
												if ($_SESSION['videook'] <> 1) {
													$msg = $_W['account']['name']." 提请您：\n▶️ 请录制视频\n";
													return $this->respText($msg);
												}
											}
										}
										if (!$this->inContext || empty($_SESSION['bmstart'])) {

											$zjrets = preg_match('/^[0-9]{1,5}$/i', $tag);
											$rets = preg_match('/'.$tcommand.'/i', $tag);
											if ($_SESSION['ok'] <> 1 && $this->message['event'] <> 'SCAN') {
												if (!$zjrets && !$rets && !is_numeric($this->message['content'])) {
													$picture = toimage($rbasic['picture']);

													return $this->respNews(array(
														'Title' => $rbasic['title'],
														'Description' => htmlspecialchars_decode($rbasic['description']),
														'PicUrl' => $picture,
														'Url' => $this->createMobileUrl('photosvote', array('rid' => $rid, 'from_user'=>$from_user)),
													));
												}
												$this->beginContext(60);//锁定60秒
												$_SESSION['ok']= 1;
												$_SESSION['content']= $this->message['content'];
												$_SESSION['code']=random(4,true);
												return $this->respText("为防止恶意刷票，请回复验证码：".$_SESSION["code"]);

											}else {
												if($this->message['content']!=$_SESSION['code'] && $this->message['event'] <> 'SCAN'){
													$_SESSION['code']=random(4,true);
													return $this->respText("验证码错误，请重新回复验证码：".$_SESSION['code']);
												}else{

													if ($this->message['event'] <> 'SCAN') {
														$tag = $_SESSION['content'];
														$this->endContext();
													}else{
														$tag = $this->message['content'];
													}

													$rets = preg_match('/'.$tcommand.'/i', $tag);
													$zjrets = preg_match('/^[0-9]{1,5}$/i', $tag);


													if ($zjrets)  {
														$tp = $this->tp($rid, $tag, $from_user, $uniacid,$rvote,$rdisplay, $rhuihua,$rbasic);
														if (is_array($tp)) {
															return $this->respNews($tp);
														}else{
															return $this->respText($tp);
														}
													}//end zjrets

													if ($rets)  {
														$isret = preg_match('/^'.$tcommand.'/i', $tag);

														if (!$isret) {
															$message = '请输入合适的格式, "'.$tcommand.'+参赛者编号(ID) 或者 参赛者真实姓名", 如: "'.$tcommand.'186 或者 '.$tcommand.'菲儿"';
															return $this->respText($message);
														}
														$ret = preg_match('/(?:'.$tcommand.')(.*)/i', $tag, $matchs);
														$word = $matchs[1];
														$tp = $this->tp($rid, $word, $from_user, $uniacid,$rvote,$rdisplay, $rhuihua,$rbasic);
														if (is_array($tp)) {
															return $this->respNews($tp);
														}else{
															return $this->respText($tp);
														}

													}//$rets

												}
											}
										}

									}
									$isuser = pdo_fetch("SELECT * FROM ".tablename($this->table_users) . " WHERE uniacid = '{$uniacid}' AND `from_user` = '{$from_user}' AND rid = '{$rid}' ");
									if (!empty($isuser)) {
										if ($rdisplay['isindex'] == 1) {
											$advs = pdo_fetch("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY id ASC LIMIT 1");
										}else {
											$advs = array();
											$advs['advname'] = $isuser['description'];
											$advs['thumb'] = $isuser['photo'];
										}
										return $this->respNews(array(
											'Title' => '您已经报过名了，点击以完善信息',
											'Description' => $advs['advname'],
											'PicUrl' => toimage($advs['thumb']),
											'Url' => $this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $from_user))
										));
									}

									if (empty($_SESSION['photoname'])) {
										$_SESSION['photoname']= $this->message['content'];
										$_SESSION['photonameok'] = 1;
										$msg = $_W['account']['name']." 提请您：\n▶️ 请回复主题介绍：";
										//$_SESSION['imageok']= 1;
										return $this->respText($msg);
									}

									if (empty($_SESSION['description'])) {
										$_SESSION['description']= $this->message['content'];
										$_SESSION['descriptionok'] = 1;
										$msg = $_W['account']['name']." 提请您：\n▶️ 请回复真实姓名：";
										//$_SESSION['imageok']= 1;
										return $this->respText($msg);
									}
									if (empty($_SESSION['realname'])) {
										$_SESSION['realname']= $this->message['content'];
										$_SESSION['realnameok'] = 1;
										$msg = $_W['account']['name']." 提请您：\n▶️ 请回复手机号码：";
										//$_SESSION['imageok']= 1;
										return $this->respText($msg);
									}

									if (empty($_SESSION['mobile'])) {
										$_SESSION['mobile']= $this->message['content'];
										$_SESSION['mobileok'] = 1;
									}

									$sql = 'SELECT uid FROM ' . tablename('mc_mapping_fans') . ' WHERE `uniacid`=:uniacid AND `openid`=:openid';
									$pars = array();
									$pars[':uniacid'] = $_W['uniacid'];
									$pars[':openid'] = $from_user;
									$uid = pdo_fetchcolumn($sql, $pars);
									$fan = pdo_fetch("SELECT avatar,nickname FROM ".tablename('mc_members') . " WHERE uniacid = '{$uniacid}' AND `uid` = '{$uid}'");
									if (!empty($fan)) {
										$avatar = $fan['avatar'];
										$nickname = $fan['nickname'];
									}
									$uuid = pdo_fetch("SELECT uid FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid AND rid = :rid ORDER BY uid DESC, id DESC LIMIT 1", array(':uniacid' => $uniacid,':rid' => $rid));

									$data = array(
										'rid'       => $rid,
										'uid'       => $uuid['uid'] + 1,
										'uniacid'      => $uniacid,
										'from_user' => $from_user,
										'avatar'    => $avatar,
										'nickname'  => $nickname,
										'music'  => $_SESSION["voiceurl"],
										'voice'  => $_SESSION["voiceurl"],
										'vedio'  => $_SESSION["videourl"],
										'description'  => $_SESSION["description"],
										'photoname'  => $_SESSION["photoname"],
										'realname'  => $_SESSION["realname"],
										'mobile'  => $_SESSION["mobile"],
										'photosnum'  => '0',
										'xnphotosnum'  => '0',
										'hits'  => '1',
										'xnhits'  => '1',
										'yaoqingnum'  => '0',
										'status'  => $rvote['tpsh'] == 1 ? '0' : '1',
										'createip' => getip(),
										'lastip' => getip(),
										'lasttime' => time(),
										'sharetime' => time(),
										'createtime'  => time()
									);
									$data['iparr'] = $this->getiparr($data['lastip']);
									pdo_insert($this->table_users, $data);

									for ($i = 1; $i <= $rvote['tpxz']; $i++) {
										if (!empty($_SESSION['imagesurl'.$i])) {
											$insertdata = array(
												'rid'       => $rid,
												'uniacid'      => $uniacid,
												'from_user' => $from_user,
												'photoname' => $_SESSION['nfilename'.$i],
												'photos' => $_SESSION['imagesurl'.$i],
												'status' => 1,
												'isfm' => 0,
												'createtime' => $now,
											);
											pdo_insert($this->table_users_picarr, $insertdata);
											//更新mid
											$lastmid = pdo_insertid();
											pdo_update($this->table_users_picarr, array('mid' => $lastmid), array('rid' => $rid,'uniacid'=> $uniacid,'from_user' => $from_user, 'id'=>$lastmid));
										}
									}

									$this->endContext();
									//$msg = $_W['account']['name']." 提请您：\n恭喜您报名成功！";
									$_SESSION['bmsuccess']= 1;
									return $this->respNews(array(
										'Title' => '恭喜'.$nickname.'报名成功！',
										'Description' => '点击以完善信息',
										'PicUrl' => toimage($avatar),
										'Url' => $this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $from_user)),
									));
									//return $this->respText($msg);

									break;
								case 'image':
									$fmmid = random(16);
									for ($i = 1; $i <= $rvote['tpxz']; $i++) {
										if (empty($_SESSION['imagesid'.$i])) {
											if ($rvote['mediatype']) {
												$_SESSION['imagesid'.$i]= $this->message['mediaid'];
												if ($rvote['mediatypev']) {
													$info = "▶️ 请开始录制您的视频";
												}
												if ($rvote['mediatypem']) {
													$info = "▶️ 请开始录制您的好声音";
												}
												if (!$rvote['mediatypev'] && !$rvote['mediatypem']) {
													$info = "▶️ 请回复您的照片主题宣言：";
												}
												$msg = $_W['account']['name']." 提请您：\n我们已经收到您的相册照片，您总共可以上传".$rvote['tpxz']."张相册照片\n"."您已经上了".$i."张相册照片\n"."如果您只想上传到当前的照片数，".$info;
												$imagesurl = $this->downloadMedia($_SESSION['imagesid'.$i], $fmmid, 'images');

												//$imagesurl = str_replace("../attachment/", '', $imagesurl);
												$_SESSION['imagesurl'.$i] = $imagesurl;
												if ($qiniu['isqiniu']) {
													$nfilename = 'FMFetchiHH'.date('YmdHis').random(16).'.jpeg';
													//$qiniu['upurl'] = $_SESSION['imagesurl'.$i];
													$qiniu['upurl'] = $_W['siteroot'].'attachment/'.$_SESSION['imagesurl'.$i];
													$mid =$i;
													//$username = pdo_fetch("SELECT photoname FROM ".tablename($this->table_users_picarr)." WHERE uniacid = :uniacid AND rid = :rid AND from_user =:from_user LIMIT 1", array(':uniacid' => $uniacid,':rid' => $rid,':from_user' => $from_user));
													$username = array();
													$username['type'] = '3';
													$qiniuimages = $this->fmqnimages($nfilename, $qiniu, $mid, $username);
													if ($qiniuimages['success'] == '-1') {
														$fmdata = array(
															"success" => -1,
															"msg" => $qiniuimages['msg'],
														);
														return $this->respText($fmdata['msg']);
													}
													$_SESSION['nfilename'.$i] = $nfilename;
													$_SESSION['imagesurl'.$i] = $qiniuimages['imgurl'];
												}
												//$msg .= "\n相册图片地址" . $i . "\n" . toimage($_SESSION['imagesurl'.$i]);
												$_SESSION['imagesok']= 1;
												return $this->respText($msg);
											}else{
												$_SESSION['imagesok']= 0;
												$msg = $_W['account']['name']." 提请您：\n本次活动未开启相册功能，请回复”报名“，按其顺序（要求）上传资料报名\n".$_W['account']['name']."感谢您的支持！";
												return $this->respText($msg);
											}
										}
									}
											if (!$rvote['mediatype']) {
												$_SESSION['imagesok']= 0;
												$msg = $_W['account']['name']." 提请您：\n本次活动未开启相册功能，请回复”报名“，按其顺序（要求）上传资料报名\n".$_W['account']['name']."感谢您的支持！";
												return $this->respText($msg);
											}
											if ($_SESSION['realnameok'] == 1) {
												$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的手机号码\n";
												//return $this->respText($msg);
											}
											if ($_SESSION['descriptionok'] == 1) {
												$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的真实姓名\n";
												//return $this->respText($msg);
											}
											if ($_SESSION['photonameok'] == 1) {
												$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的主题介绍\n";
												//return $this->respText($msg);
											}

											if ($rvote['mediatypev'] == 1) {
												if ($_SESSION['videook']) {
													$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的照片主题宣言\n";
													//return $this->respText($msg);
												}
											}

											if ($rvote['mediatypem'] == 1) {
												if ($_SESSION['voiceok']) {
													$info = $_W['account']['name']." 提请您：\n▶️ 请录制您的视频\n";
													//return $this->respText($msg);
												}
											}
											if ($rvote['mediatype'] == 1) {
												if ($_SESSION['imagesok']) {
													$info = $_W['account']['name']." 提请您：\n▶️ 请录制您的好声音\n";
													//return $this->respText($msg);
												}
											}
											$msg = $_W['account']['name']." 提请您：\n我们已经收到您的相册照片，您总共可以上传".$rvote['tpxz']."张相册照片\n"."您已经上了".$rvote['tpxz']."张相册照片\n"."".$info;
											//$_SESSION['imagesok']= 1;
											return $this->respText($msg);

									break;

								case 'voice':
									$fmmid = random(16);
									if (empty($_SESSION['voiceid'])) {
										$_SESSION['voiceid']= $this->message['mediaid'];
										if ($rvote['mediatypev']) {
											$info = "▶️ 请开始录制您的视频";
										}else{
											$info = "▶️ 请回复您的照片主题宣言：";
										}


										$voiceurl = $this->downloadMedia($_SESSION['voiceid'], $fmmid, 'voice');
										$_SESSION['voiceurl'] = $voiceurl;
										if ($qiniu['isqiniu']) {
											$nfilename = 'FMVOICEHH'.date('YmdHis').random(16).'.amr';
											$upurl = tomedia($_SESSION['voiceurl']);
											$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
											$audiotype = 'voice';
												$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upurl, $audiotype, $username);
												$nfilenamefop = $qiniuaudios['nfilenamefop'];
												if ($qiniuaudios['success'] == '-1') {
												//	var_dump($err);
													$fmdata = array(
														"success" => -1,
														"msg" => $qiniuaudios['msg'],
													);
													return $this->respText($fmdata['msg']);
												} else {
													$insertdata = array();

													if ($qiniuaudios['success'] == '-2') {
														//var_dump($err);
														$fmdata = array(
																"success" => -1,
																"msg" => $qiniuaudios['msg'],
															);
															return $this->respText($fmdata['msg']);
													} else {

														$voice = $qiniuaudios[$audiotype];
														//$udata[$audiotype] = $qiniuaudios[$audiotype];

														//pdo_insert($this->table_users_voice, $udata);
														//pdo_update($this->table_users, array('fmmid' => $fmmid,'mediaid'  =>$_POST['serverId'],'lastip' => getip(),'lasttime' => $now,'voice' => $voice,'timelength' => $_GPC['timelength']), array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));

														if ($username) {
															$insertdataname = array();
															$insertdataname[$audiotype.'name'] = $nfilename;
															$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
															pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
														}else {
															$insertdataname = array(
																'rid'       => $rid,
																'uniacid'      => $uniacid,
																'from_user' => $from_user,
															);
															$insertdataname[$audiotype.'name'] = $nfilename;
															$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
															pdo_insert($this->table_users_name, $insertdataname);
														}
													}
												}
											$_SESSION['voiceurl'] = $voice;
										}
										$msg = $_W['account']['name']." 提请您：\n我们已经收到您录制的好声音\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请重新录制好声音\n";
										//$msg .= "\n好声音地址" . "\n" . tomedia($_SESSION['voiceurl']);


										$_SESSION['voiceok']= 1;
										return $this->respText($msg);
									}else{



										$_SESSION['voiceid']= $this->message['mediaid'];

										if ($_SESSION['realnameok']) {
											$info = "▶️ 请回复您的手机号码\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['descriptionok']) {
											$info = "▶️ 请回复您的真实姓名\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['photonameok']) {
											$info = "▶️ 请回复您的主题介绍\n";
											//return $this->respText($msg);
										}

										if ($rvote['mediatypev']) {
											if ($_SESSION['videook']) {
												$info = "▶️ 请回复您的照片主题宣言\n";
												//return $this->respText($msg);
											}
										}

										if ($rvote['mediatypem']) {
											if ($_SESSION['voiceok']) {
												$info = "▶️ 请录制您的视频\n";
												//return $this->respText($msg);
											}
										}
										$msg = $_W['account']['name']." 提请您：\n我们已经收到您重新录制的好声音\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请再次重新录制好声音\n";
										$voiceurl = $this->downloadMedia($_SESSION['voiceid'], $fmmid, 'voice');
										load()->func('file');
										file_delete($_SESSION['voiceurl']);
										$_SESSION['voiceurl'] = $voiceurl;

										//return $this->respText($qiniu);
										if ($qiniu['isqiniu']) {
											$nfilename = 'FMVOICEHH'.date('YmdHis').random(16).'.amr';
											$upurl = tomedia($_SESSION['voiceurl']);
											$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
											$audiotype = 'voice';
												$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upurl, $audiotype, $username);
												$nfilenamefop = $qiniuaudios['nfilenamefop'];
												if ($qiniuaudios['success'] == '-1') {
												//	var_dump($err);
													$fmdata = array(
														"success" => -1,
														"msg" => $qiniuaudios['msg'],
													);
													return $this->respText($fmdata['msg']);
												} else {
													$insertdata = array();

													if ($qiniuaudios['success'] == '-2') {
														//var_dump($err);
														$fmdata = array(
																"success" => -1,
																"msg" => $qiniuaudios['msg'],
															);
															return $this->respText($fmdata['msg']);
													} else {

														$voice = $qiniuaudios[$audiotype];
														//$udata[$audiotype] = $qiniuaudios[$audiotype];

														//pdo_insert($this->table_users_voice, $udata);
														//pdo_update($this->table_users, array('fmmid' => $fmmid,'mediaid'  =>$_POST['serverId'],'lastip' => getip(),'lasttime' => $now,'voice' => $voice,'timelength' => $_GPC['timelength']), array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));

														if ($username) {
															$insertdataname = array();
															$insertdataname[$audiotype.'name'] = $nfilename;
															$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
															pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
														}else {
															$insertdataname = array(
																'rid'       => $rid,
																'uniacid'      => $uniacid,
																'from_user' => $from_user,
															);
															$insertdataname[$audiotype.'name'] = $nfilename;
															$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
															pdo_insert($this->table_users_name, $insertdataname);
														}
													}
												}
											$_SESSION['voiceurl'] = $voice;
										}
										//$msg .= "\n好声音地址" . "\n" . tomedia($_SESSION['voiceurl']);
										$_SESSION['voiceok']= 1;
										return $this->respText($msg);
									}

									break;
								case 'video':

									$fmmid = random(16);
									if (empty($_SESSION['videoid'])) {
										$_SESSION['videoid']= $this->message['mediaid'];
										$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
										$_SESSION['videourl'] = $videourl;

										if ($qiniu['isqiniu']) {	//开启七牛存储
											$audiotype = 'vedio';
											$nfilename = 'FMHH'.date('YmdHis').random(8).'hhvideo.mp4';
											$upmediatmp = toimage($_SESSION['videourl']);

											$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username);
											$nfilenamefop = $qiniuaudios['nfilenamefop'];
											if ($qiniuaudios['success'] == '-1') {
											//	var_dump($err);
												$fmdata = array(
													"success" => -1,
													"msg" => $qiniuaudios['msg'],
												);
												return $this->respText($fmdata['msg']);
											} else {
												$insertdata = array();

												if ($qiniuaudios['success'] == '-2') {
													//var_dump($err);
													$fmdata = array(
															"success" => -1,
															"msg" => $err,
														);
														return $this->respText($fmdata['msg']);
												} else {
													//var_dump($ret);
													$insertdata[$audiotype] = $qiniuaudios[$audiotype];
													//pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
													if ($username) {
														$insertdataname = array();
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
													}else {
														$insertdataname = array(
															'rid'       => $rid,
															'uniacid'      => $uniacid,
															'from_user' => $from_user,
														);
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_insert($this->table_users_name, $insertdataname);
													}

												}
											}
											$_SESSION['videourl'] = $qiniuaudios[$audiotype];
										}

										$_SESSION['videook']= 1;

										$info = "▶️ 请回复您的照片主题宣言：";

										$msg = $_W['account']['name']." 提请您：\n我们已经收到您录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请重新录制视频\n";
										//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
										return $this->respText($msg);
									}else{
										//$this->istip('video');
										$_SESSION['videoid']= $this->message['mediaid'];
										load()->func('file');
										file_delete($_SESSION['videourl']);
										$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
										$_SESSION['videourl'] = $videourl;

										if ($qiniu['isqiniu']) {	//开启七牛存储
											$audiotype = 'vedio';
											$nfilename = 'FMHH'.date('YmdHis').random(8).'hhvideo.mp4';

											$upmediatmp = toimage($_SESSION['videourl']);
											$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username);
											$nfilenamefop = $qiniuaudios['nfilenamefop'];

											if ($qiniuaudios['success'] == '-1') {
											//	var_dump($err);
												$fmdata = array(
													"success" => -1,
													"msg" => $qiniuaudios['msg'],
												);
												return $this->respText($fmdata['msg']);
											} else {
												$insertdata = array();

												if ($qiniuaudios['success'] == '-2') {
													//var_dump($err);
													$fmdata = array(
															"success" => -1,
															"msg" => $err,
														);
														return $this->respText($fmdata['msg']);
												} else {
													//var_dump($ret);
													$insertdata[$audiotype] = $qiniuaudios[$audiotype];


													if ($username) {
														$insertdataname = array();
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
													}else {
														$insertdataname = array(
															'rid'       => $rid,
															'uniacid'      => $uniacid,
															'from_user' => $from_user,
														);
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_insert($this->table_users_name, $insertdataname);
													}
												}
											}
											$_SESSION['videourl'] = $qiniuaudios[$audiotype];
										}

										if ($_SESSION['realnameok']) {
											$info = "▶️ 请回复您的手机号码\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['descriptionok']) {
											$info = "▶️ 请回复您的真实姓名\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['photonameok']) {
											$info = "▶️ 请回复您的主题介绍\n";
											//return $this->respText($msg);
										}

										if ($rvote['mediatypev']) {
											if ($_SESSION['videook']) {
												$info = "▶️ 请回复您的照片主题宣言\n";
												//return $this->respText($msg);
											}
										}

										$msg = $_W['account']['name']." 提请您：\n我们已经收到您重新录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请再次重新录制视频\n";
										//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
										$_SESSION['videook']= 1;
										return $this->respText($msg);
									}
									break;
								case 'shortvideo':

									$fmmid = random(16);
									if (empty($_SESSION['videoid'])) {
										$_SESSION['videoid']= $this->message['mediaid'];
										$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
										$_SESSION['videourl'] = $videourl;
										$_SESSION['videook']= 1;

										$info = "▶️ 请回复您的照片主题宣言：";

										$msg = $_W['account']['name']." 提请您：\n我们已经收到您录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请重新录制视频\n";
										//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
										return $this->respText($msg);
									}else{
										//$this->istip('video');
										$_SESSION['videoid']= $this->message['mediaid'];
										load()->func('file');
										file_delete($_SESSION['videourl']);
										$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
										$_SESSION['videourl'] = $videourl;

										if ($_SESSION['realnameok']) {
											$info = "▶️ 请回复您的手机号码\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['descriptionok']) {
											$info = "▶️ 请回复您的真实姓名\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['photonameok']) {
											$info = "▶️ 请回复您的主题介绍\n";
											//return $this->respText($msg);
										}

										if ($rvote['mediatypev']) {
											if ($_SESSION['videook']) {
												$info = "▶️ 请回复您的照片主题宣言\n";
												//return $this->respText($msg);
											}
										}

										$msg = $_W['account']['name']." 提请您：\n我们已经收到您重新录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请再次重新录制视频\n";
										//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
										$_SESSION['videook']= 1;
										return $this->respText($msg);
									}
									break;

								default:

								break;
							}
						}




					}//总的结束

				}else{

					if($now <= $rbasic['start_time']){
						$message = "亲，".$rbasic['title']."活动将在".date("Y-m-d H:i:s", $rbasic['start_time'])."时准时开放投票,您可以：\n";
						$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&rid=".$rid."&m=fm_photosvote&do=photosvote'>先睹为快</a>\n";
						if (($rhuihua['ishuodong'] == 1 || $rhuihua['ishuodong'] == 3) && !empty($rhuihua['huodongurl'])) {
							$message .= "2、<a href='".$rhuihua['huodongurl']."'>".$rhuihua['huodongname']."</a>";
						}

					}elseif($now >= $rbasic['end_time']){
						$message = "亲，".$rbasic['title']."活动已经于".date("Y-m-d H:i:s", $rbasic['end_time'])."时结束,您可以：\n";
						$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&rid=".$rid."&m=fm_photosvote&do=paihang'>看看排行榜</a>\n";
						if (($rhuihua['ishuodong'] == 1 || $rhuihua['ishuodong'] == 3) && !empty($rhuihua['huodongurl'])) {
							$message .= "2、<a href='".$rhuihua['huodongurl']."'>".$rhuihua['huodongname']."</a>";
						}

					}
					return $this->respText($message);
				}


	}

	public function isNeedSaveContext() {
		return false;
	}
	public function tp($rid, $word, $from_user, $uniacid,$rvote,$rdisplay, $rhuihua,$rbasic) {
		global $_W;
		$now = time();
		if ($now <= $rbasic['tstart_time']) {
			return $rbasic['ttipstart'];
		}
		if ($now >= $rbasic['tend_time']) {
			return $rbasic['ttipend'];
		}
		$starttime=mktime(0,0,0);//当天：00：00：00
		$endtime = mktime(23,59,59);//当天：23：59：59
		$times = '';
		$times .= ' AND createtime >=' .$starttime;
		$times .= ' AND createtime <=' .$endtime;

		if (is_numeric($word)) {
			$where .= " AND uid = '".$word."'";
			$ckword = $word . '号';
		}else{
			$where .= " AND realname = '".$word."'";
			$ckword = $word;
		}

		$t = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and rid = :rid ".$where." LIMIT 1", array(':uniacid' => $uniacid,':rid' => $rid));
		if (empty($t)) {
			$message = '未找到 '.$ckword.' ，请重新输入！';
			$message .= "<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&rid=".$rid."&m=fm_photosvote&do=photosvote&from_user=".$from_user."'>活动首页</a>\n";

			return $message;
		}
		if($t['status']!='1'){
			$message = '您投票的用户'.$ckword.'还未通过审核，请稍后再试,您可以：';
			$message .= "<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&rid=".$rid."&m=fm_photosvote&do=photosvote&from_user=".$from_user."'>活动首页</a>\n";
			return $message;
		}

		$tfrom_user = $t['from_user'];
		if ($tfrom_user == $from_user) {//不能给自己投票
			//message('您不能为自己投票',referer(),'error');
			$message = '您不能为自己投票';
			return $message;
		}
		if (!empty($rvote['limitsd']) && !empty($rvote['limitsdps'])) {// 全体投票限速
			$zf = date('H',time()) * 60 + date('i',time());
			$timeduan = intval((1440 / $rvote['limitsd'])*($zf / 1440));//总时间段 288 当前时间段
			$cstime = $timeduan*$rvote['limitsd'] * 60+mktime(0,0,0);//初始限制时间
			$jstime = ($timeduan+1)*$rvote['limitsd'] * 60+mktime(0,0,0);//结束限制时间

			$tptimes = '';
			$tptimes .= ' AND createtime >=' .$cstime;
			$tptimes .= ' AND createtime <=' .$jstime;
			$limitsdvote = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_log).' WHERE tfrom_user = :tfrom_user AND rid = :rid '.$tptimes.' ORDER BY createtime DESC', array(':tfrom_user' => $tfrom_user, ':rid' => $rid));	// 全体当前时间段投票总数
			if ($cstime > 0) {
				if ($limitsdvote >= $rvote['limitsdps']) {
					$msg = '亲，投票的速度太快了';
					return $message;
				}
			}
		}
		if (!empty($t['limitsd'])){//个人单独投票限速
			$zf = date('H',time()) * 60 + date('i',time());
			$timeduan = intval((1440 / $t['limitsd'])*($zf / 1440));//总时间段 288 当前时间段
			$cstime = $timeduan*$t['limitsd'] * 60+mktime(0,0,0);//初始限制时间
			$jstime = ($timeduan+1)*$t['limitsd'] * 60+mktime(0,0,0);//结束限制时间


			$tptimesgr = '';
			$tptimesgr .= ' AND createtime >=' .$cstime;
			$tptimesgr .= ' AND createtime <=' .$jstime;
			$limitsdvotegr = pdo_fetchcolumn('SELECT COUNT(1) FROM '.tablename($this->table_log).' WHERE tfrom_user = :tfrom_user AND rid = :rid '.$tptimesgr.' ORDER BY createtime DESC', array(':tfrom_user' => $tfrom_user, ':rid' => $rid));	//每几分钟投几票 个人
			if ($t['limitsd'] > 0)  {
				if ($limitsdvotegr >= 1) {
					$msg = '亲，您投票的速度太快了';
					return $message;
				}
			}
		}

		$tpxz_status = $this->gettpxz_status($rid, $from_user, $tfrom_user, '1', $rvote['fansmostvote']);
		if (!$tpxz_status) { //活动期间一共可以投多少次票限制（全部人）
			$message = '在此活动期间，你总共可以投 '.$rvote['fansmostvote'].' 票，目前你已经投完！';
			return $message;
		}
		$tpxz_status = $this->gettpxz_status($rid, $from_user, $tfrom_user, '2', $rvote['daytpxz']);//每天总共投票的次数限制（全部人）
		if (!$tpxz_status) {
			$message = "您当天最多可以给她投票".$rvote['dayonetp']."次，您已经投完，请明天再来。您还可以：\n";
			$message .= "<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&tfrom_user=".$tfrom_user."&rid=".$rid."&m=fm_photosvote&do=tuser'>围观Ta</a>\n";
			return $message;
		}
		$tpxz_status = $this->gettpxz_status($rid, $from_user, $tfrom_user, '3', $rvote['allonetp']);
		if (!$tpxz_status) {//在活动期间，给某个人总共投的票数限制（单个人）
			$message = "您总共可以给她投票".$rvote['allonetp']."次，您已经投完！您还可以：\n";
			$message .= "<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&tfrom_user=".$tfrom_user."&rid=".$rid."&m=fm_photosvote&do=tuser'>围观Ta</a>\n";
			return $message;
		}
		$tpxz_status = $this->gettpxz_status($rid, $from_user, $tfrom_user, '4', $rvote['dayonetp']);
		if (!$tpxz_status) {//每天总共可以给某个人投的票数限制（单个人）
			$message = "您当天最多可以给她投票".$rvote['dayonetp']."次，您已经投完，请明天再来。您还可以：\n";
			$message .= "<a href='".$_W['siteroot']."app/index.php?i=".$uniacid."&c=entry&tfrom_user=".$tfrom_user."&rid=".$rid."&m=fm_photosvote&do=tuser'>围观Ta</a>\n";
			return $message;
		}
		if ($_W['account']['level'] != 1) {
			$access_token = WeAccount::token();
			$urls = sprintf("https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN", $access_token,$from_user);
			$contents = ihttp_get($urls);
			$dats = $contents['content'];
			$re = @json_decode($dats, true);

			$nickname = $re['nickname'];
			$avatar = $re['headimgurl'];
		}
		$nickname =empty($nickname) ? $this->getname($rid, $from_user) : $nickname;
		$avatar =empty($avatar) ? $this->getname($rid, $from_user,'10', 'avatar') : $avatar;
		$votedate = array(
			'uniacid' => $uniacid,
			'rid' => $rid,
			'tptype' => '2',
			'avatar' => $avatar,
			'nickname' => $nickname,
			'from_user' => $from_user,
			'afrom_user' => $_COOKIE["user_fromuser_openid"],
			'tfrom_user' => $tfrom_user,
			'ip' => getip(),
			'createtime' => time(),
		);
		$votedate['iparr'] = '来自微信会话界面';
		pdo_insert($this->table_log, $votedate);
		$this->counter($rid, $from_user, $tfrom_user,'tp',$rvote['unimoshi']);
		$this->addjifen($rid, $from_user, $tfrom_user,array($nickname,$avatar,$sex),array($uniacid, '1', $rdisplay['ljtp_total'],$t['photosnum'],$t['hits'],$rdisplay['cyrs_total']));

		$tname = $this->getname($rid, $tfrom_user);

		$message = "恭喜您为 ".$t['uid']." 参赛者  ".$tname."  投了一票！\n";
		$rowtp = array();
		$rowtp['title'] = $message;
		$rowtp['description'] =htmlspecialchars_decode($message);
		$rowtp['picurl'] = getphotos($t['avatar'], $tfrom_user, $rid, 'photos', $this->table_users_picarr);
		$rowtp['url'] =  $this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $tfrom_user));

		$news[] = $rowtp;
		if ($rdisplay['isindex'] == 1) {
			$advs = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC LIMIT 6");

			foreach($advs as $c) {
				$rowadv = array();
				$rowadv['title'] = $c['advname'];
				$rowadv['description'] = $c['description'];
				$rowadv['picurl'] = toimage($c['thumb']);
				$rowadv['url'] = empty($c['link']) ? $this->createMobileUrl('photosvote', array('rid' => $rid)) : $c['link'];
				$news[] = $rowadv;
			}
		}

		if (($rhuihua['ishuodong'] == 1 || $rhuihua['ishuodong'] == 3) && !empty($rhuihua['huodongurl'])) {
			$rowhd = array();
			$rowhd['title'] = $rhuihua['huodongname'];
			$rowhd['description'] = $rhuihua['huodongdes'];
			$rowhd['picurl'] = toimage($rhuihua['hhhdpicture']);
			$rowhd['url'] = $rhuihua['huodongurl']."&from=fm_photosvote&oid=".$from_user;
			$news[] = $rowhd;
		}
		return $news;
	}

	public function addjifen($rid, $from_user, $tfrom_user,$info = array(),$vote = array(),$type='vote') {
		if ($type != 'reg') {
			pdo_update($this->table_users, array('photosnum'=> $vote['3']+$vote['1'],'hits'=> $vote['4']+$vote['1']), array('rid' => $rid, 'from_user' => $tfrom_user));
			pdo_update($this->table_reply_display, array('ljtp_total' => $vote['2']+$vote['1'],'cyrs_total' => $vote['5']+$vote['1']), array('rid' => $rid));//增加总投票 总人气
		}
		$rjifen = pdo_fetch("SELECT is_open_jifen,is_open_jifen_sync,jifen_vote,jifen_vote_reg,jifen_reg FROM ".tablename($this->table_jifen)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		if ($rjifen['is_open_jifen']) {
			if ($type == 'reg') {
				$jifen = $rjifen['jifen_reg'];
				$msg = '报名参赛 <span class="label label-warning">增加</span> '.$jifen.'积分';
				$voteer = pdo_fetch("SELECT jifen FROM ".tablename($this->table_voteer)." WHERE from_user = :from_user and rid = :rid limit 1", array(':from_user' => $from_user,':rid' => $rid));
			}else{
				$user = pdo_fetch("SELECT id FROM ".tablename($this->table_users)." WHERE from_user = :from_user and rid = :rid limit 1", array(':from_user' => $tfrom_user,':rid' => $rid));
				if (!empty($user)) {
					$tjifen = $rjifen['jifen_vote_reg']*$vote['1'];
					$tmsg = '被投票 <span class="label label-warning">增加</span> '.$tjifen.'积分';
				}
				$jifen = $rjifen['jifen_vote']*$vote['1'];
				$msg = '投票 <span class="label label-warning">增加</span> '.$jifen.' 积分';
				$voteer = pdo_fetch("SELECT jifen FROM ".tablename($this->table_voteer)." WHERE from_user = :from_user and rid = :rid limit 1", array(':from_user' => $from_user,':rid' => $rid));
				$tvoteer = pdo_fetch("SELECT jifen FROM ".tablename($this->table_voteer)." WHERE from_user = :from_user and rid = :rid limit 1", array(':from_user' => $tfrom_user,':rid' => $rid));
			}


			if ($rjifen['is_open_jifen_sync']) {
				load()->model('mc');
				$uid = mc_openid2uid($from_user);
				if (empty($uid)) {
					$uid = $_W['fans']['uid'];
				}
				if (!empty($uid)) {
					mc_credit_update($uid, 'credit1', $jifen, array(0, $msg,'fm_photosvote'));
					$result = mc_fetch($uid, array('credit1'));
					$lastjifen = $result['credit1'];
				}else{
					$lastjifen = $voteer['jifen']+$jifen;
				}
				$tuid = mc_openid2uid($tfrom_user);
				if (!empty($tuid)) {
					mc_credit_update($tuid, 'credit1', $tjifen, array(0, $tmsg,'fm_photosvote'));
					$tresult = mc_fetch($tuid, array('credit1'));
					$tlastjifen = $tresult['credit1'];
				}else{
					$lastjifen = $voteer['jifen']+$jifen;
					$tlastjifen = $tvoteer['jifen']+$tjifen;
				}
			}else{
				$lastjifen = $voteer['jifen']+$jifen;
				$tlastjifen = $tvoteer['jifen']+$tjifen;
			}

			pdo_update($this->table_voteer, array('jifen' => $lastjifen), array('rid' => $rid, 'from_user'=>$from_user));//增加积分

			if ($type != 'reg') {
				pdo_update($this->table_voteer, array('jifen' => $tlastjifen), array('rid' => $rid, 'from_user'=>$tfrom_user));//增加积分
			}
		}
		if ($type == 'reg') {
			$this->addmsg($rid,$from_user,'','报名消息',$msg,'3');
		}else{
			//$tpinfo = $this->gettpinfo($rid,$from_user);
			$nickname = $this->getname($rid, $from_user);
			$tcontent = '恭喜您，' . $nickname . '为您投了'.$vote['1'].'票<br />' . $tmsg;
			$this->addmsg($rid,$from_user,$tfrom_user,'投票消息',$info['3'],'1');
			$this->addmsg($rid,$tfrom_user,'','被投票消息',$tcontent,'2');
		}
		return true;
	}
	public function addmsg($rid,$from_user, $tfrom_user, $title, $content, $type = '1') {
		global $_W;
		$date = array(
			'uniacid' => $_W['uniacid'],
			'rid' => $rid,
			'status' => '0',
			'type' => $type,
			'from_user' => $from_user,
			'tfrom_user' => $tfrom_user,
			'title' => $title,
			'content' => $content,
			'createtime' => time()
		);
		pdo_insert($this->table_msg, $date);
	}

	public function _getuser($rid, $tfrom_user, $uniacid = '') {
		global $_GPC, $_W;
		return pdo_fetch("SELECT uid, avatar, nickname, realname, sex, mobile FROM ".tablename($this->table_users)." WHERE rid = :rid and from_user = :tfrom_user ", array(':rid' => $rid, ':tfrom_user' => $tfrom_user));
	}
	public function gettpinfo($rid, $from_user) {
		$tpinfo = pdo_fetch('SELECT realname, mobile,nickname,avatar FROM '.tablename($this->table_voteer).' WHERE rid= :rid AND from_user = :from_user ', array(':rid' => $rid,':from_user' => $from_user));
		return $tpinfo;
	}
	public function getname($rid, $from_user, $limit = '20' , $type = 'name') {
		load()->model('mc');
		if ($type == 'avatar') {
			$username = $this->_getuser($rid, $from_user);
			$avatar = tomedia($username['avatar']);
			if (empty($avatar)) {

				$username = $this->gettpinfo($rid, $from_user);
				$avatar = tomedia($username['avatar']);
				if (empty($avatar)) {
					$username = mc_fansinfo($from_user);
					$avatar = tomedia($username['avatar']);
					if (empty($avatar)) {
						$avatar = tomedia('./addons/fm_photosvote/icon.jpg');
					}
				}
			}
			return $avatar;
		}else{

			$username = $this->_getuser($rid, $from_user);
			if (!empty($username['realname'])) {
				$name = cutstr($username['realname'], $limit);
			}else{
				$name = cutstr($username['nickname'], $limit);
			}
			if (empty($name)) {

				$username = $this->gettpinfo($rid, $from_user);
				if (!empty($username['realname'])) {
					$name = cutstr($username['realname'], $limit);
				}else{
					$name = cutstr($username['nickname'], $limit);
				}
				if (empty($name)) {
					$username = mc_fansinfo($from_user);
					$name = cutstr($username['nickname'], $limit);
					if (empty($name)) {
						$name = cutstr($from_user, $limit);
						if (empty($name)) {
							$name = '网友';
						}
					}
				}
			}
			return $name;
		}
	}

	public function gettpxz_status($rid, $from_user, $tfrom_user = '', $type = '1', $tpxz) {
	
		
		$counter = $this->gettpnum($rid, $from_user, $tfrom_user, $type);//活动期间一共可以投多少次票限制（全部人）
		
		if ($counter >= $tpxz) {
			return false;
		}else{
			return true;
		}
	}
	public function gettpnum($rid, $from_user, $tfrom_user = '', $type = '') {
		global $_W;
		$where = "";
		$starttime = mktime(0,0,0);//当天：00：00：00
		$endtime = $starttime + 86399;//当天：23：59：59
		$where .= ' AND createtime >=' .$starttime;
		$where .= ' AND createtime <=' .$endtime;
		
		switch ($type) {
			case '1':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = :type", array(':rid' => $rid,':from_user' => $from_user,':type' => $type));
				break;
			case '2':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = :type $where", array(':rid' => $rid,':from_user' => $from_user,':type' => $type));
				break;
			case '3':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND tfrom_user = :tfrom_user AND type = :type", array(':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));

				break;
			case '4':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND type = :type $where", array(':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));

				break;
			case '5':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user AND type = :type", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':type' => $type));
				break;
			case '6':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user AND type = :type $where", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':type' => $type));

				break;
			case '7':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user AND tfrom_user = :tfrom_user AND type = :type", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));

				break;
			case '8':
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND type = :type $where", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));
				break;
			case '9':
				$counter = pdo_fetchcolumn("SELECT gift_times FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = 9", array(':rid' => $rid,':from_user' => $from_user));
				break;

			default:
				$counter = pdo_fetchcolumn("SELECT tp_times FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = :type", array(':rid' => $rid,':from_user' => $from_user,':type' => $type));
				break;
		}
		if (empty($counter)) {
			$counter = 0;
		}
		return $counter;
	}
	public function counter($rid,$from_user,$tfrom_user, $types, $unimoshi='') {
		global $_W;
		$where = "";
		$starttime = mktime(0,0,0);//当天：00：00：00
		$endtime = $starttime + 86399;//当天：23：59：59
		$where .= ' AND createtime >=' .$starttime;
		$where .= ' AND createtime <=' .$endtime;
		if ($types == 'tp') {
			if ($unimoshi == 1) {
				$num = 8;
			}else{
				$num = 4;
			}
			for ($type = 1; $type <= $num; $type++) {
				$date = array(
					'uniacid' => $_W['uniacid'],
					'rid' => $rid,
					'from_user' => $from_user
				);
				switch ($type) {
					case '1':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = :type", array(':rid' => $rid,':from_user' => $from_user,':type' => $type));
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '2':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = :type $where", array(':rid' => $rid,':from_user' => $from_user,':type' => $type));
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '3':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND tfrom_user = :tfrom_user AND type = :type", array(':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));
						$date['tfrom_user'] = $tfrom_user;
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '4':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND type = :type $where", array(':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));
						$date['tfrom_user'] = $tfrom_user;
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '5':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user AND type = :type", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':type' => $type));
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '6':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user AND type = :type $where", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':type' => $type));
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '7':
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user AND tfrom_user = :tfrom_user AND type = :type", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));
						$date['tfrom_user'] = $tfrom_user;
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					case '8':
						$starttime = mktime(0,0,0);//当天：00：00：00
						$endtime = $starttime + 86399;//当天：23：59：59
						$where .= ' AND createtime >=' .$starttime;
						$where .= ' AND createtime <=' .$endtime;
						$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE uniacid = :uniacid AND rid = :rid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND type = :type $where", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':type' => $type));
						$date['tfrom_user'] = $tfrom_user;
						$date['tp_times'] = $counter['tp_times'] + 1;
						$date['type'] = $type;
						break;
					default:
						break;
				}

				$id = $counter['id'];
				if (empty($id)) {
					$date['createtime'] = TIMESTAMP;
					pdo_insert($this->table_counter, $date);
				}else{
					pdo_update($this->table_counter, $date, array('id' => $id));
				}
			}
		}elseif ($types == 'gift') {
			$date = array(
				'uniacid' => $_W['uniacid'],
				'rid' => $rid,
				'from_user' => $from_user,
				'type' => 9,
			);
			$counter = pdo_fetch("SELECT * FROM ".tablename($this->table_counter)." WHERE rid = :rid AND from_user = :from_user AND type = :type", array(':rid' => $rid,':from_user' => $from_user,':type' => 9));
			$date['gift_times'] = $counter['gift_times'] + 1;
			$id = $counter['id'];
			if (empty($id)) {
				$date['createtime'] = TIMESTAMP;
				pdo_insert($this->table_counter, $date);
			}else{
				pdo_update($this->table_counter, $date, array('id' => $id));
			}
		}
	}

}



