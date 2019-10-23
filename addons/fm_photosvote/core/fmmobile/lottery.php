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

//查询是否存在了
$user = pdo_fetch('SELECT * FROM '.tablename('fm_photosvote_wm_user').' WHERE rid = :rid and from_user = :from_user LIMIT 1', array('rid' => $rid,'from_user' => $from_user));

$filed = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_wm_upload')." WHERE rid = :rid LIMIT 1", array(':rid' => $rid));
$prize = unserialize($filed['prize']);

$count = 0;
foreach($prize as $k=>$v){
	if( $v['number'] == 0){
		$prize[$k]['pro'] = 0;
	}
	$count += $v['number'];
}
$i=0;
foreach($prize as $k=>$v){
	if( substr($v['img'], 0, 7) == 'images/' ){
		$prize[$k]['img'] = '../attachment/'.$prize[$k]['img'];
	}
	$prize_arr[$i]['id'] = $k;
	$prize_arr[$i]['i'] = $i;
	$prize_arr[$i]['number'] = $v['number'];
	$prize_arr[$i]['pro'] = $v['pro'];
	$prize_arr[$i]['prize'] = $v['name'];
	$prize_arr[$i]['lv'] = $v['lv'];
	$prize_arr[$i]['v'] = ceil($count*($v['pro']/100));
	$i++;
}

/**
*设置中奖概率
*/
function get_rand($proArr) {   
    $result = '';    
    //概率数组的总概率精度   
    $proSum = array_sum($proArr);    
    //概率数组循环   
    foreach ($proArr as $key => $proCur) {   
        $randNum = mt_rand(1, $proSum);   
        if ($randNum <= $proCur) {   
            $result = $key;   
            break;   
        } else {   
            $proSum -= $proCur;   
        }         
    }   
    unset ($proArr);    
    return $result;   
}

foreach ($prize_arr as $key => $val) {
	if($val['v'] != '0' && $val['pro'] != 0 && $val['number'] != 0 ){
		$arr[$val['id']] = $val['v'];   
	}
}   
$lrid = get_rand($arr); //根据概率获取奖项id
$data = $prize_arr[$lrid-1]; //中奖项


//今天凌晨的时间戳
$todayTime =  strtotime(date('Y-m-d'));
//剩余抽奖次数
$lotteryNumber = $filed['lotterynum'];
//是否已经用完抽奖次数
$use = false;
//每日投票次数上限
$daytpxz = pdo_getcolumn('fm_photosvote_reply_vote', array('rid' => $rid), 'daytpxz',1);
//已经使用了的次数
$useNumber = pdo_fetchcolumn("SELECT COUNT(id) FROM ".tablename('fm_photosvote_votelog')." WHERE rid = :rid and  from_user = :from_user and createtime > ".$todayTime." LIMIT 1", array(':rid' => $rid,':from_user' => $from_user));
//已经使用的次数大于等于投票上限说明投票完成可以参与投票
if ($useNumber >= $daytpxz) {
	$use = true;
	//查询今天已经用过的抽奖次数
	$useLottery = pdo_fetchcolumn("SELECT COUNT(id) FROM ".tablename('fm_photosvote_wm_lottery_log')." WHERE rid = :rid and  from_user = :from_user and time > ".$todayTime." LIMIT 1", array(':rid' => $rid,':from_user' => $from_user));
	//还剩余的次数。
	$lotteryNumber = $filed['lotterynum']-$useLottery;
}else{
	$lotteryNumber = 0;
}

//开始抽奖
if( $_GPC['start'] == 1){
	$fmdata = array("success" => -1,
		"msg" => '抽奖失败',
	);

	if( $data['id'] < $filed['lotterystart'] ){
		//查询用户已经中过的奖品
		$repeatLottery = pdo_fetchcolumn("SELECT COUNT(id) FROM ".tablename('fm_photosvote_wm_lottery_log')." WHERE rid = :rid and  from_user = :from_user and lid < :lid LIMIT 1", array(':rid' => $rid,':from_user' => $from_user,':lid' => $filed['lotterystart']));
		//在判断是否重复中奖
		$continueLottery = pdo_fetchcolumn("SELECT COUNT(id) FROM ".tablename('fm_photosvote_wm_lottery_log')." WHERE rid = :rid and  from_user = :from_user and lid = :lid LIMIT 1", array(':rid' => $rid,':from_user' => $from_user,':lid' => $data['id']));
	}
	
	
	if($use == false){
		$fmdata['msg'] = '对不起，请完成每日投票后再来进行抽奖！';
	}else if($lotteryNumber == 0 ){
		$fmdata['msg'] = '对不起，您今日的抽奖次数已经用完，请明日再来！';
	//中奖后不能继续中奖
	}else if( ($filed['lotterycontinue'] == 0 &&  $continueLottery >= 1) || ($filed['lotteryrepeat'] == 0 &&  $repeatLottery >= 1)){
		$data = $prize_arr[$filed['lotterystart']-1];
		$fmdata['data'] = $data;
		$fmdata['msg'] = '抽奖成功！';
		$fmdata['success'] = 1;
		
		//插入抽奖记录
		$logData['rid'] = $rid;
		$logData['from_user'] = $from_user;
		$logData['prize'] = $data['lv'].$data['prize'];
		$logData['lid'] = $data['id'];
		$logData['time'] = time();
		pdo_insert('fm_photosvote_wm_lottery_log', $logData);
	//开始抽奖
	}else{
		$fmdata['data'] = $data;
		$fmdata['msg'] = '抽奖成功！';
		$fmdata['success'] = 1;
		//插入抽奖记录
		$logData['rid'] = $rid;
		$logData['from_user'] = $from_user;
		$logData['prize'] = $data['lv'].$data['prize'];
		$logData['lid'] = $data['id'];
		$logData['time'] = time();
		pdo_insert('fm_photosvote_wm_lottery_log', $logData);
		//减少剩余数量
		if( $prize[$data['id']]['number'] > 0)
		{
			$prize[$data['id']]['number'] = $prize[$data['id']]['number']-1;
			pdo_update('fm_photosvote_wm_upload', array('prize'=>serialize($prize)), array('rid' => $rid));
		}
	}
	echo json_encode($fmdata);
	exit();
}

$templatename = $rbasic['templates'];
if ($templatename != 'default' && $templatename != 'stylebase') {
	require FM_CORE. 'fmmobile/tp.php';
}
$toye = $this->templatec($templatename,$_GPC['do']);
include $this->template($toye);

