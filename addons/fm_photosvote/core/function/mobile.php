<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
require IA_ROOT. '/addons/fm_photosvote/core/defines.php';
require FM_CORE. 'version.php';
require FM_CORE. 'function/core.php';
require FM_CORE. 'function/function.php';
require IA_ROOT. '/addons/fm_photosvote/site.php';
class Mobile extends Fm_photosvoteModuleSite {
	function dwz($url) {
		load()->func('communication');
		$dc = ihttp_post('http://dwz.cn/create.php', array('url'=> $url));
		$t = @json_decode($dc['content'], true);	
		return $t['tinyurl'];
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
	function downloadImage($mediaid, $filename) {
		//下载图片	
		global $_W;
		$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_W['acid'];		
		load()->func('file');
		$access_token = WeAccount::token();
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = $this->downloadWeixinFile($url);
		$updir = '../attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/';		
		if(!is_dir($updir)){ 
			mkdirs($updir);	
		}  
		$filename = $updir.$filename.".jpg"; 
		$this->saveWeixinFile($filename, $fileInfo["body"]);
		return $filename;
	}
	function downloadVoice($mediaid, $filename, $savetype = 0) {
		//下载语音		
		global $_W;
		load()->func('file');
		$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_W['acid'];	
		
		$access_token = WeAccount::token();
		
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = $this->downloadWeixinFile($url);	
				
		$updir = '../attachment/audios/'.$uniacid.'/'.date("Y").'/'.date("m").'/';		
		if(!is_dir($updir)){ 
			mkdirs($updir);	
		}  
		//$key = $filename.".mp3";
		$filename = $updir.$filename.".amr";
		
		$this->saveWeixinFile($filename, $fileInfo["body"]);
		//$localfilename = $_W['siteroot'].'attachment/audios/'.$uniacid.'/'.date("Y").'/'.date("m").'/'.$key;
		//$qimedia = $this->qiniusaveWeixinFile($key , $localfilename, $fileInfo["body"], $rid);
		if ($savetype == 1) {
			return $qimedia;
		} else {
			return $filename;
		}
		
		
	}
	function downloadThumb($mediaid, $filename) {
		//下载缩略图
		global $_W;
		load()->func('file');
		$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_W['acid'];		
		$access_token = WeAccount::token();
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = $this->downloadWeixinFile($url);
		$updir = '../attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/';		
		if(!is_dir($updir)){ 
			mkdirs($updir);	
		}  
		$filename = $updir.$filename.".jpg"; 
		$this->saveWeixinFile($filename, $fileInfo["body"]);
		return $filename;
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


	function get_share($uniacid,$rid,$from_user,$title) {
		
		
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT xuninum,hits FROM ".tablename($this->table_reply_display)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			
			$csrs = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."");
			
			
		   			
			$listtotal = $csrs + $reply['hits'] + pdo_fetchcolumn("SELECT sum(hits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnhits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + $reply['xuninum'];//总参与人数
			
			
			$ljtp = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename($this->table_log)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnphotosnum) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."");//累计投票
			
        }		
		if (!empty($from_user)) {
		    $userinfo = pdo_fetch("SELECT uid, nickname,realname FROM ".tablename($this->table_users)." WHERE rid= :rid AND from_user= :from_user", array(':rid' => $rid,':from_user' => $from_user));
			$nickname = empty($userinfo['realname']) ? $userinfo['nickname'] : $userinfo['realname'];
			$userid = $userinfo['uid'];
		}
		$str = array('#编号#'=>$userid,'#参赛人数#'=>$csrs,'#参与人数#'=>$listtotal,'#参与人名#'=>$nickname,'#累计票数#'=>$ljtp);
		$result = strtr($title,$str);
        return $result;
    }

} 
