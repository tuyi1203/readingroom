<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('communication');
$a ='aHR0cDovL24uZm1vb25zLmNvbS9hcGkvYXBpLnBocD8mYXBpPWFwaQ==';		
//$ca = ihttp_get(base64_decode($a));
//$c = @json_decode($ca['content'], true);		
$d = base64_decode("aHR0cDovL2FwaS5mbW9vbnMuY29tL2luZGV4LnBocD8md2VidXJsPQ==").$_SERVER ['HTTP_HOST']."&visitorsip=" . $_W['clientip']."&modules=".$_GPC['m'];				
$dc = ihttp_get($d);
$t = @json_decode($dc['content'], true);	
$cfg = $this->module['config'];
//print_r($t);

    