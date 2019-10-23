<?php
	
	active();

function active(){

		$startTime = this_friday()+16*3600+1800;
		
		$startEnd = $startTime+24*3600;
		
		//判读是否开始
		dataStart($time,$startTime);
		//判断是否结束
		dateEnd($time,$endtime);	

		
	
}

function dataStart($time,$startTime){

 		if( $time<$startTime ){

			$ret['code'] = '-1';
			$ret['message'] = '活动未开始，请您耐心等待';

			die(json_encode($ret,JSON_UNESCAPED_UNICODE));
		}
}

function dateEnd($time,$endtime){

	if( $time>$endtime ){

		$ret['code'] = '-2';
		
		$ret['message'] = '对不起，活动已结束！';

		die(json_encode($ret,JSON_UNESCAPED_UNICODE));
	}

} 


function this_monday($timestamp=0,$is_return_timestamp=true){
 	static $cache ;


	 $id = $timestamp.$is_return_timestamp;

	 if(!isset($cache[$id])){

		 if(!$timestamp) $timestamp = time();

		 $time= $timestamp-24*3600*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-6*86400);

		 $monday_date = date('Y-m-d',$time);

		 if($is_return_timestamp){

		 	$cache[$id] = strtotime($monday_date);
		 
		 }else{
		
			 $cache[$id] = $monday_date;
		
		 }
	 }
	 return $cache[$id];

 }

 //获取本周周五
 function this_friday($timestamp=0,$is_return_timestamp=true){

	 static $cache ;

	 $id = $timestamp.$is_return_timestamp;

	 if(!isset($cache[$id])){

	 if(!$timestamp) $timestamp = time();

	 $friday = this_monday($timestamp) + 4*86400;

		 if($is_return_timestamp){

			 $cache[$id] = $friday ;
		 }else{

		 	$cache[$id] = date('Y-m-d',$friday);
		 
		 }
	 }

	 return $cache[$id];
}




?>