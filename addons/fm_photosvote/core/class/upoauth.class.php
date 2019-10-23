<?php
/**
 * 上游用户信息类
 * @author 未梦
 * @url http://www.weimengcms.com/
 */
class UpOauth{
	//加密密码
	private $key = '123456789';
	private $token;
	private $timestamp;
	function __construct()
	{
		global $_GPC;
		$this->token = $_GPC['token'];
		$this->timestamp = $_GPC['timestamp'];
		//token不正确或者时间戳不再十秒内请求。
		if( md5($this->timestamp.$this->key) != $this->token || ($this->timestamp > time()-10) || ($this->timestamp > time()))
		{
			//die('token error');
		}
	}
	
	function GetOauth()
	{
		$usersinfo['from_user'] = 'shangyou';
		$usersinfo['follow'] = '0';
		$usersinfo['nickname'] = '上游APP用户';
		$usersinfo['avatar'] = '';
		$usersinfo['sex'] = 1;
		
		return $usersinfo;
	}
}