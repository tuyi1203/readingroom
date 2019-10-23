<?php
if(!function_exists('getdaytotal')) {
	function getdaytotal($stime, $etime, $tablename,$rid) {
		global $_W;
		$uniacid = $_W['uniacid'];
		$dayarray=array("日","一","二","三","四","五","六"); 
		$today = TIMESTAMP;
		$days = array();
		$n = ceil(($etime - $stime)/86400)+1;
		for ($i = 0; $i < $n; $i++) {
			$time = $today - $i*24*3600;
			$day = '星期'.$dayarray[date("w",$time)];
			$starttime=mktime(0,0,0) - $i*24*3600;//当天：00：00：00
			$endtime = mktime(23,59,59) - $i*24*3600;//当天：23：59：59
			$condition = '';
			//$condition .= ' uniacid ='.$uniacid;
			$condition .= ' rid ='.$rid;
			$condition .= ' AND createtime <'.$endtime;
			$condition .= ' AND createtime >'.$starttime;
			$sql = 'SELECT COUNT(1) FROM ' . tablename($tablename) . ' WHERE ' . $condition;
			$totalday = pdo_fetchcolumn($sql);
			$days[] = array('day' => $day.' ('.date('Y-m-d', $time).')','totals' => $totalday);
		}
		krsort($days);
		

		return $days;
	}
}

if(!function_exists('gettongji')) {
	function gettongji($type, $stime='', $etime='',$tablename,$rid) {
		
		switch ($type) {
			case 'day':
				$tongjis = getdaytotal($stime, $etime,$tablename,$rid);
				break;
			case 'week':
				$tongjis = getweektotal();
				break;
			case 'month':
				$tongjis = getmonthtotal();
				break;
			case 'jidu':
				$tongjis = getjidutotal();
				break;
			case 'fanyi':
				$tongjis = getfanyitotal();
				break;
			case 'xingji':
				$tongjis = getxingjitotal();
				break;
			
			default:
				$tongjis = getmonthtotal();
				break;
		}
		

		$tongji = array();
		$tongji['0'] = '';
		$tongji['0'] .= '[';
		$i = 1;
		foreach ($tongjis as $key => $value) {
			
			if ($i == 1) {
				$tongji['0'] .= "'";
			}else {
				$tongji['0'] .= ",'";
			}
			
			$tongji['0'] .= $value['day']."'";
			
			$i++;
		}
		$tongji['0'] .= ']';

		$tongji['1'] = '';
		$tongji['1'] .= '[';
		$i = 1;
		foreach ($tongjis as $key => $value) {
			if ($i == 1) {
				$tongji['1'] .= "['";
			}else {
				$tongji['1'] .= ",['";
			}
			
			$tongji['1'] .= $value['day']."',".$value['totals'].']';
			
			

			$i++;
		}
		$tongji['1'] .= ']';
		return $tongji;
	}
}
