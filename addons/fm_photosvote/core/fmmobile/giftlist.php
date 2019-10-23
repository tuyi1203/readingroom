<?php
/**
 * 女神来了模块定义
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 * (c) Copyright 2016 FantasyMoons. All Rights Reserved.
 */
defined('IN_IA') or exit('Access Denied');
//查询自己是否参与活动
if (!empty($from_user)) {
	$user = pdo_fetch("SELECT realname, nickname, avatar FROM " . tablename($this -> table_users) . " WHERE from_user = :from_user and rid = :rid", array(':from_user' => $from_user, ':rid' => $rid));
	$voteer = pdo_fetch("SELECT nickname,realname,avatar FROM " . tablename($this -> table_voteer) . " WHERE rid = :rid AND from_user = :from_user", array(':rid' => $rid, ':from_user' => $from_user));
	$gift = $this->getgift($rid);
	$mygift = $this->getmygift($rid, $from_user);
	if ($_GPC['foo'] == 'giftshop') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		//$total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . ' WHERE rid = :rid AND from_user = :from_user '.$uni.'', array(':rid' => $rid,':from_user' => $from_user));
		$data = pdo_fetchall("SELECT * FROM " . tablename($this -> table_jifen_gift) . ' WHERE rid = :rid ' . $uni . ' ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid));
		foreach ($data as $key => $value) {
			$data[$key]['title'] = cutstr($value['gifttitle'], '4');
			$data[$key]['des'] = empty($value['description']) ?  $value['gifttitle'] : $value['description'] ;
			$data[$key]['images'] = tomedia($value['images']);
		}
	} else {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 200;

		if ($_GPC['gifttype'] == 'yishiyong') {
			$data = pdo_fetchall("SELECT * FROM " . tablename($this->table_user_zsgift) . ' WHERE rid = :rid AND from_user = :from_user ' . $uni . ' ORDER BY id ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid, ':from_user' => $from_user));

			foreach ($data as $key => $value) {
				$g = pdo_fetch("SELECT * FROM " . tablename($this -> table_jifen_gift) . ' WHERE id = :id ' . $uni . '', array(':id' => $value['giftid']));

				$data[$key]['title'] = cutstr($g['gifttitle'], '4');
				$data[$key]['des'] = empty($g['description']) ?  $g['gifttitle'] : $g['description'] ;
				$data[$key]['images'] = tomedia($g['images']);
				$data[$key]['lasttime'] = date('m-d H:i', $value['lasttime']);
				$data[$key]['piaoshu'] = $g['piaoshu'];
				$data[$key]['jifen'] = $g['jifen'];


				$data[$key]['status'] = '<div class="mui-pull-right mui-btn mui-btn-warning" style="right: 0;">已兑换</div>';
				$data[$key]['cstatus'] = '<div class="mui-badge mui-badge-warning" style="position: absolute;top: 10px;" >已使用</div>';
				$data[$key]['time'] = '<div class="mui-input-row" style="height:40px;"><label>使用时间</label><input type="text" value="'.date('Y-m-d h:i:s', $value['lasttime']).'" readonly="readonly"></div>';
				$data[$key]['tuser'] = '<div class="mui-input-row" style="height:40px;"><label>已送给</label><img class="ysimg" src="'.$this->getname($rid, $value['tfrom_user'],'20', 'avatar').'"><span class="ystext">'.$this->getname($rid, $value['tfrom_user']).'</span></div>';

			}


		}else{
			$data = pdo_fetchall("SELECT * FROM " . tablename($this->table_user_gift) . ' WHERE rid = :rid AND from_user = :from_user ' . $uni . ' ORDER BY status ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $rid, ':from_user' => $from_user));

			foreach ($data as $key => $value) {
				$g = pdo_fetch("SELECT * FROM " . tablename($this -> table_jifen_gift) . ' WHERE id = :id ' . $uni . '', array(':id' => $value['giftid']));

				$data[$key]['title'] = cutstr($g['gifttitle'], '4');
				$data[$key]['des'] = empty($g['description']) ?  $g['gifttitle'] : $g['description'] ;
				$data[$key]['images'] = tomedia($g['images']);
				$data[$key]['lasttime'] = date('m-d H:i', $value['lasttime']);
				$data[$key]['piaoshu'] = $g['piaoshu'];
				$data[$key]['jifen'] = $g['jifen'];
				if ($value['status'] == 1) {
					$data[$key]['status'] = '<div class="mui-pull-right mui-btn mui-btn-success" style="right: 0;">未使用('.$value['giftnum'].')</div>';
					$data[$key]['cstatus'] = '<div class="mui-badge mui-badge-success" style="position: absolute;top: 10px;">未使用('.$value['giftnum'].')</div>';
					$data[$key]['time'] = '<div class="mui-input-row" style="height:40px;"><label>兑换时间</label><input type="text" value="'.date('Y-m-d h:i:s', $value['lasttime']).'" readonly="readonly"></div>';
				}
				$data[$key]['tuser'] = '';
			}
		}

	}
	$unrname = $this -> getusernames($user['realname'], $voteer['realname'], '6', $voteer['nickname']);

	$title = $unrname . ' 的礼物中心';

}

if ($_GPC['foo'] == 'giftshop') {
	if ($_GPC['op'] == 'sub') {
		include $this -> template('giftshop_sub');
	} else {
		include $this -> template('giftshop');
	}
} else {
	if ($_GPC['op'] == 'sub') {

		include $this -> template('giftlist_sub');
	} else {
		$templatename = $rbasic['templates'];
		if ($templatename != 'default' && $templatename != 'stylebase') {
			require FM_CORE . 'fmmobile/tp.php';
		}
		$toye = $this -> templatec($templatename, $_GPC['do']);
		include $this -> template($toye);
	}
}
