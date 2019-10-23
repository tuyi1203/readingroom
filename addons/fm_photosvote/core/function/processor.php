<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */

	function GetIpLookup($ip = ''){
		if(empty($ip)){
			$ip = getip();
		}
		$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
		if(empty($res)){ return false; }
		$jsonMatches = array();
		preg_match('#\{.+?\}#', $res, $jsonMatches);
		if(!isset($jsonMatches[0])){ return false; }
		$json = json_decode($jsonMatches[0], true);
		if(isset($json['ret']) && $json['ret'] == 1){
			$json['ip'] = $ip;
			unset($json['ret']);
		}else{
			return false;
		}
		return $json;
	}
	function getiparr($ip) {
		$ip = GetIpLookup($ip);
		$iparr = array();
		$iparr['country'] .= $ip['country'];
		$iparr['province'] .= $ip['province'];
		$iparr['city'] .= $ip['city'];
		$iparr['district'] .= $ip['district'];
		$iparr['ist'] .= $ip['ist'];
		$iparr = iserializer($iparr);
		return $iparr;
	}
	function getphotos($avatar, $from_user, $rid, $is = '',$fm_photosvote_provevote_picarr) {
		$photo = getpicarrp($rid, $from_user,'1',$fm_photosvote_provevote_picarr);
		if ($is == 'avatar') {
			if (!empty($avatar)) {
				$photos = tomedia($avatar);
			}elseif (!empty($photo)) {
				$photos = tomedia($photo['photos']);
			}else{
				$photos = tomedia($picture);
			}
		}else {
			if (!empty($photo)) {
				$photos = tomedia($photo['photos']);
			}elseif (!empty($avatar)) {
				$photos = tomedia($avatar);
			}else{
				$photos = tomedia($picture);
			}
		}
		return $photos;
	}
	function getpicarrp($rid, $from_user,$isfm = '', $fm_photosvote_provevote_picarr) {
		if ($isfm == 1) {
			$photo = pdo_fetch("SELECT photos,photoname,imgpath FROM ".tablename($fm_photosvote_provevote_picarr)." WHERE from_user = :from_user AND rid = :rid AND isfm = :isfm LIMIT 1", array(':from_user' => $from_user,':rid' => $rid,':isfm' => $isfm));
		}else {
			$photo = pdo_fetch("SELECT photos,photoname,imgpath FROM ".tablename($fm_photosvote_provevote_picarr)." WHERE from_user = :from_user AND rid = :rid ORDER BY createtime DESC LIMIT 1", array(':from_user' => $from_user,':rid' => $rid));
		}
		return $photo;
	}
	function fmqnimages($nfilename, $qiniu, $mid, $username) {
		$fmurl = 'http://api.fmoons.com/api/qiniu/api.php?';
		$hosts = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$host = base64_encode($hosts);

		$visitorsip = base64_encode(getip());

		$fmimages = array(
			'nfilename' => $nfilename,
			'qiniu' => $qiniu,
			'mid' => $mid,
			'username' => $username,
		);

		$fmimages =  base64_encode(base64_encode(iserializer($fmimages)));

		$fmpost = $fmurl.'host='.$host."&visitorsip=" . $visitorsip."&webname=" . $webname."&fmimages=".$fmimages;

		load()->func('communication');
		$content = ihttp_get($fmpost);
		$fmmv = @json_decode($content['content'], true);

		if ($mid == 0) {

			$fmdata = array(
				"success" => $fmmv['success'],
				"msg" =>$fmmv['msg'],
			);
			$fmdata['mid'] == 0;
			$fmdata['imgurl'] = $fmmv['imgurl'];

			return $fmdata;
			exit;

		}else{
			$fmdata = array(
				"success" => $fmmv['success'],
				"msg" => $fmmv['msg'],
			);
			$fmdata['picarr_'.$mid] = $fmmv['picarr_'.$mid];
			return $fmdata;
			exit;
		}
		//return $fmmv;
	}

	function fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username) {
		global $_W;
		$fmurl = 'http://api.fmoons.com/api/qiniu/api.php?';
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$host = base64_encode($host);
		$clientip = base64_encode($_W['clientip']);

		$fmaudios = array(
			'nfilename' => $nfilename,
			'qiniu' => $qiniu,
			'upmediatmp' => $upmediatmp,
			'audiotype' => $audiotype,
			'username' => $username,
		);
		$fmaudios =  base64_encode(base64_encode(iserializer($fmaudios)));

		$fmpost = $fmurl.'host='.$host."&visitorsip=" . $clientip."&fmaudios=".$fmaudios;

		load()->func('communication');
		$content = ihttp_get($fmpost);
		$fmmv = @json_decode($content['content'], true);

		$fmdata = array(
			"msg" => $fmmv['msg'],
			"success" => $fmmv['success'],
			"nfilenamefop" => $fmmv['nfilenamefop'],
		);
		$fmdata[$audiotype] = $fmmv[$audiotype];

		return $fmdata;
		exit();

	}

	function downloadMedia($mediaid, $filename, $type) {
		//下载语音
		global $_W;
		load()->func('file');
		$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_W['acid'];
		$access_token = WeAccount::token();
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = downloadWeixinFile($url);
		if ($type == 'images') {
			$typepath = "images";
		}else {
			$typepath = "audios";
		}
		$path = "{$typepath}/{$uniacid}/" . date('Y/m/');
		mkdirs(ATTACHMENT_ROOT . '/' . $path);
		if ($type == 'images') {
			$filenames = $path ."HHimages_" . $filename . ".jpeg";
		}
		if ($type == 'voice') {
			$filenames = $path ."HHvoice_" . $filename . ".amr";
		}
		if ($type == 'video') {
			$filenames = $path ."HHvideo_" . $filename . ".mp4";
		}


		saveWeixinFile(ATTACHMENT_ROOT . '/' . $filenames, $fileInfo["body"]);

		return $filenames;


	}

	function downloadWeixinFile($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		curl_close($ch);
		$imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));
		return $imageAll;
	}

	function saveWeixinFile($filename, $filecontent) {
		$local_file = fopen($filename, 'w');
		if (false !== $local_file){
			if (false !== fwrite($local_file, $filecontent)) {
				fclose($local_file);
			}
		}
	}

