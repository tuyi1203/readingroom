<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');

		$item_per_page = $_GPC['pagesnum'];
		$page_number = intval($_GPC['page']);
				if(!is_numeric($page_number)){
				 header('HTTP/1.1 500 Invalid page number!');
					exit();
				}
				$position = ($page_number * $item_per_page);

				$m = $position+1;;



				if ($_GPC['phdata'] == 'phb') {
					$where = '';
					$where .= " AND status = '1'";
					$tagid = $_GPC['tagid'];
					$tagpid = $_GPC['tagpid'];
					$tagtid = $_GPC['tagtid'];
					if (!empty($tagid)) {
						$where .= " AND tagid = '".$tagid."'";
					}
					if (!empty($tagpid)) {
						$where .= " AND tagpid = '".$tagpid."'";
					}
					if (!empty($tagtid)) {
						$where .= " AND tagtid = '".$tagtid."'";
					}
					if ($rdisplay['indexpx'] == '0') {
						$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
					}elseif ($rdisplay['indexpx'] == '1') {
						$where .= " ORDER BY `createtime` DESC";

					}elseif ($rdisplay['indexpx'] == '2') {
						$where .= " ORDER BY `hits` + `xnhits` DESC";
					}else{
						$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
					}
					$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE rid = :rid '.$where.'  LIMIT ' . $position . ',' . $item_per_page, array(':rid' => $rid) );


					//output results from database
					if (!empty($userlist)){

						foreach ($userlist as $mid => $row) {
							if ($row['realname']){
								$usernames = cutstr($row['realname'], '10');
							}elseif ($row['nickname']){
								$usernames = cutstr($row['nickname'], '10');
							}else{
								$usernames = cutstr($row['from_user'], '10');
							}

								$result = $result.'<li>';
									$result = $result.'<a href="'.$this->createMobileUrl('tuserphotos', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'" target="_blank"><div style="width:15%;  text-align: center;" >';


								$mmid = $m+$mid;
								if ($page_number == 0) {


									if ($mid >= 0 && $mid < 3) {
										$pid = 'one'.($mid + 1);
									}else{
										$pid = 'two';
									}

									$result = $result.'<i class="'.$pid.'">';
									if ($mid >= 3) {
										$mida = $mid+1;
										$result = $result.$mida;
									}
									$result = $result.'</i>';

								}else{

									$result = $result.'<i class="two">'.$mmid.'</i>';
								}


								$result = $result.'</div><div style="width:35%;  text-align: center;" class="btext"><span style="  text-align: left;  overflow: hidden;">';
								$result = $result.'<img src="'.toimage($row['avatar']).'" width="30" style="max-height:30px;   border-radius: 135px;"/> '.$usernames.'</span></div>';
								$pxnum = $row['photosnum'] + $row['xnphotosnum'];
								$result = $result.'<div style="width:25%;  text-align: center;" class="bnum">'.$pxnum.'</div>';
								$hxnum = $row['hits'] + $row['xnhits'];
								$result = $result.'<div style="width:25%;  text-align: center;" class="bnum">'.$hxnum.'</div>';

								$result = $result.'</a></li>';


						}


					}

				}else{

					$where = '';
					if (!empty($tfrom_user)) {
						$where .= " AND tfrom_user = '".$tfrom_user."'";
					}
					$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE rid = :rid AND is_del = 0 '.$where.' ORDER BY `id` DESC LIMIT ' . $position . ',' . $item_per_page, array(':rid' => $rid) );


					//output results from database
					if (!empty($userlist)){

						foreach ($userlist as $mid => $row) {
							if ($row['realname']){
								$usernames = cutstr($row['realname'], '5');
							}elseif ($row['nickname']){
								$usernames = cutstr($row['nickname'], '5');
							}else{
								$usernames = cutstr($row['from_user'], '5');
							}


								$result = $result.'<li><a href="javascript::;"><div style="width:20%;">';


								$mmid = $m+$mid;

									$result = $result.'<i class="two">'.$mmid.'</i>';


								$result = $result.'</div><div style="width:45%; " class="btext">';
								$result = $result.'<img src="'.toimage($row['avatar']).'" width="30" style=" max-height:30px;  border-radius: 135px;"/> '.$usernames.'</div>';
								$ctime = date('Y-m-d H:i:s', $row['createtime']);
								$result = $result.'<div style="width:35%;  line-height: 20px;" class="bnum">'.$ctime.'</div>';
								$result = $result.'</a></li>';

						}
					}

				}
				print_r($result);