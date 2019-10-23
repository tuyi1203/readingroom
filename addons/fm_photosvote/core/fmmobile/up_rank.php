<?php
/**
 * 选手排行榜
 *
 * @author 未梦
 * @url http://www.weimengcms.com/
 */
 /**
 * pagesnum 每页多少条
	phdata 默认排行榜
	page 页数
 */
defined('IN_IA') or exit('Access Denied');

$_GPC['phdata'] = 'phb';
		$item_per_page = empty($_GPC['pagesnum']) ? 10 : $_GPC['pagesnum'];
		$page_number = intval($_GPC['page']);
				if(!is_numeric($page_number)){
				 header('HTTP/1.1 500 Invalid page number!');
					exit();
				}
				$position = ($page_number * $item_per_page);

				$m = $position+1;
				if ($_GPC['phdata'] == 'phb') {
					$tagid = $_GPC['tagid'];
					$tagpid = $_GPC['tagpid'];
					$tagtid = $_GPC['tagtid'];
					if ($_GPC['op'] == 'tags') {
						$where = '';
						update_tags_piaoshu($rid);

						if (!empty($tagid)) {
							$where .= " AND parentid = " . $tagid;
							$where .= " AND icon = 1";
						}elseif (!empty($tagpid)) {
							$where .= " AND parentid = " . $tagpid;
							$where .= " AND icon = 2";
						}elseif (!empty($tagtid)) {
							$where .= " AND parentid = " . $tagtid;
							$where .= " AND icon = 3";
						}

						$tags = pdo_fetchall("SELECT id, title, piaoshu FROM ".tablename($this->table_tags)." WHERE rid = :rid  ".$where." ORDER BY piaoshu DESC  LIMIT " . $position . ',' . $item_per_page, array(':rid' => $rid));
						if (!empty($tags)){

							foreach ($tags as $mid => $row) {
									$result[$mid]['usernames'] = cutstr($row['title'], '10');
									$mmid = $m+$mid;
									if ($page_number == 0) {
										if ($mid >= 0 && $mid < 3) {
											$pid = 'one'.($mid + 1);
										}else{
											$pid = 'two';
										}
										$result[$mid]['pid'] = $pid;
									}else{
										$result[$mid]['pid'] = $mmid;
									}
									if (!$rdisplay['open_vote_count']) {
										if (!empty($rdisplay['open_vote_size'])) {
											if ($rdisplay['open_vote_size'] > $mmid) {
												$pxnum = $row['piaoshu'];
											}else{
												$pxnum = '****';
											}
										}else{
											$pxnum = '****';
										}
									}else{
										$pxnum = $row['piaoshu'];
									}
									$result[$mid]['pxnum'] = $pxnum;
									$result[$mid]['hxnum'] = $row['hits'] + $row['xnhits'];
							}
						}
					}else{

						$where = '';
						$where .= " AND status = '1'";
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
							$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC, `hits` + `xnhits` DESC";
						}elseif ($rdisplay['indexpx'] == '1') {
							$where .= " ORDER BY `createtime` DESC, `photosnum` + `xnphotosnum` DESC, `hits` + `xnhits` DESC";

						}elseif ($rdisplay['indexpx'] == '2') {
							$where .= " ORDER BY `hits` + `xnhits` DESC, `photosnum` + `xnphotosnum` DESC";
						}else{
							$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC, `hits` + `xnhits` DESC";
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
								$result[$mid]['usernames'] = $usernames;
								$result[$mid]['ftrom_user'] = $row['from_user'];
									$mmid = $m+$mid;
									if ($page_number == 0) {
										if ($mid >= 0 && $mid < 3) {
											$pid = 'one'.($mid + 1);
										}else{
											$pid = 'two';
										}
										$result[$mid]['pid'] = $pid;
									}else{
										$result[$mid]['pid'] = $mmid;
									}
									$result[$mid]['avatar'] = toimage($row['avatar']);

									if (!$rdisplay['open_vote_count']) {
										if (!empty($rdisplay['open_vote_size'])) {
											if ($rdisplay['open_vote_size'] > $mmid) {
												$pxnum = $row['photosnum'] + $row['xnphotosnum'];
											}else{
												$pxnum = '****';
											}
										}else{
											$pxnum = '****';
										}
									}else{
										$pxnum = $row['photosnum'] + $row['xnphotosnum'];
									}
									$result[$mid]['pxnum'] = $pxnum;
									$result[$mid]['hxnum'] = $hxnum;
							}
						}
					}
				}else{

					$where = '';
					if (!empty($tfrom_user)) {
						$where .= " AND tfrom_user = '".$tfrom_user."'";
					}
					$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE rid = :rid '.$where.' ORDER BY `id` DESC LIMIT ' . $position . ',' . $item_per_page, array(':rid' => $rid) );


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


								$result = $result.'<li><a href="javascript::;">';

								$result = $result.'<div style="width:35%; " class="btext">';
								$result = $result.'<img src="'.toimage($row['avatar']).'" width="30" style=" max-height:30px;  border-radius: 135px;"/> '.$usernames.'</div>';
								$result = $result.'<div style="width:17%;  line-height: 20px;text-align:center;" class="bnum">'.$row['vote_times'].'</div>';
								if ($row['is_del'] == 1) {
									$status = '<span style="color:red;" >无效</span>';
								}else{
									$status = '正常';
								}
								$result = $result.'<div style="width:17%;  line-height: 20px;text-align:center;" class="bnum">'.$status.'</div>';

								$ctime = date('Y-m-d H:i:s', $row['createtime']);
								$result = $result.'<div style="width:30%;  line-height: 20px;text-align:center;" class="bnum">'.$ctime.'</div>';
								$result = $result.'</a></li>';

						}
					}

				}
				echo json_encode($result);
				exit;