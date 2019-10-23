<?php

 $template_id = $fmqftemplate['fmqftemplate'];//消息模板id 微信的模板id
 $body = "";
 $keyword1 = "";

 if (!empty($template_id)) {
	
	$uname = $u['nickname'];
	
	$k2 = $item['description'];
	$ttime = date('Y-m-d H:i:s', $item['createtime']);
  //  $body .= "您的姓名：{$u['nickname']} \n";
 //   $body .= "被投票ID：{$u['uid']} \n";
  //  $body .= "被投票用户：$uname \n";
    $body .= "文章描述：$k2 \n";
    $body .= "更多精彩的内容正在等着您，快来查看吧 ☟";
      
   
	$title = $u['nickname'].'  “'.$item['title'].'”已经发布。';
	$datas=array(
		'first'=>array('value'=>$title,'color'=>'#1587CD'),
		'keyword1'=>array('value'=>$item['title'],'color'=>'#1587CD'),
		'keyword2'=>array('value'=>$ttime,'color'=>'#173177'),
		'remark'=> array('value'=>$body,'color'=>'#FF9E05')
	);
	$data=json_encode($datas); //发送的消息模板数据
}
?>
	