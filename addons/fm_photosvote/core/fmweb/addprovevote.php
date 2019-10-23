<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');
		$display = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$vote = pdo_fetch("SELECT * FROM ".tablename($this->table_reply_vote)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		$reply = array_merge($display, $vote); 
		$uid = pdo_fetch("SELECT uid FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid AND rid = :rid ORDER BY uid DESC, id DESC LIMIT 1", array(':uniacid' => $uniacid,':rid' => $rid));
			if (checksubmit('submit')) {
				
				$now = time();


				$isuid = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and uid = :uid and rid = :rid", array(':uniacid' => $uniacid,':uid' => $_GPC['uid'],':rid' => $rid));
				if (!empty($isuid)) {
					message('此ID已经存在，请重新填写！');
				}			
				if (empty($_GPC['photoname'])) {
					message('照片主题名没有填写！');
				}
				/**if (empty($_GPC['description'])) {
					message('介绍没有填写');
				}
				if (empty($_GPC['realname'])) {
					message('您的真实姓名没有填写，请填写！');
				}
				if(!preg_match(REGULAR_MOBILE, $_GPC['mobile'])) {
					message('必须输入手机号，格式为 11 位数字。');
				}
					**/		
				$realname = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and realname = :realname and rid = :rid", array(':uniacid' => $uniacid,':realname' => $_GPC['realname'],':rid' => $rid));
				if (!empty($realname)) {
					message('您的真实姓名已经存在，请重新填写！');
				}
				$ymobile = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and mobile = :mobile and rid = :rid", array(':uniacid' => $uniacid,':mobile' => $_GPC['mobile'],':rid' => $rid));
				if(!empty($ymobile)) {
					message('非常抱歉，此手机号码已经被注册，你需要更换注册手机号！');
				}
				
				
				$insertdata = array(
					'rid'       => $rid,
					'uid'       => empty($_GPC['uid']) ? $uid['uid']+ 1 : $_GPC['uid'],
					'uniacid'      => $uniacid,
					'from_user' => random(16).$now,
					'avatar'    => $_GPC["avatar"],
					'nickname'  => $_GPC["realname"],	    
					'photo'  => $_GPC["photo"],	
					'music' => $_GPC['music'],
					'vedio' => $_GPC['vedio'],	
					'description'  => htmlspecialchars_decode($_GPC['description']),
					'photoname'  => $_GPC["photoname"],
					'realname'  => $_GPC["realname"],
					'mobile'  => $_GPC["mobile"],
					'weixin'  => $_GPC["weixin"],
					'qqhao'  => $_GPC["qqhao"],
					'email'  => $_GPC["email"],
					'job'  => $_GPC["job"],
					'xingqu'  => $_GPC["xingqu"],
					'address'  => $_GPC["address"],
					'photosnum' => '0',
					'xnphotosnum' => intval($_GPC['xnphotosnum']),	    
					'hits'  => '1',
					'xnhits'  =>  intval($_GPC['xnhits']),
					'yaoqingnum'  => '0',
					'sharenum'  => '0',
					'createip' => getip(),
					'lastip' => getip(),
					'status'  =>intval($_GPC['status']),
					'sharetime' =>'',
					'createtime'  => $now,
				);
				//多图上传
				$picarrTmp = array();
				for ($i = 1; $i <= $reply['tpxz']; $i++) {
					$picarrTmp[] .= $_GPC['picarr_'.$i];	
				}
				$insertdata['picarr'] = iserializer($picarrTmp);
				
				pdo_insert($this->table_users, $insertdata);
				message('报名用户添加成功！', $this->createWebUrl('members', array('rid' => $rid, 'foo' => 'display')), 'success');
				
			}
		
		include $this->template('web/addprovevote');
