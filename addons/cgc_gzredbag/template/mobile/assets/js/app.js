/**
 * 晨报发红包
 * 
 * @author kmoon
 * @version1.0 2016-12-24
 */

//设置是否全屏
define(["jquery"],function($){
	function isfull(){
		if(isFull == true){
			document.addEventListener('touchmove', function(event) {
				event.preventDefault();
			}, false);
			$('html,body').css({'height':'100%','width':'100%','overflow':'hidden'});
		}
	}
	
	function draw(){
		$('#_openBtn').bind('click',function(){
			$('#_submitLoad').show();
			$('#_openBtn').attr('disabled',true);
			url = 'http://cw.cqcbw.com/mywe/app/index.php?i=73&c=entry&do=Testlist&m=cgc_gzredbag';
			$.post(url,{},function(data){
				$('#_submitLoad').css('display','none');
	                $('#_luckPage').fadeOut();
				if (data.code==-1) {
					
					emptyMoney();

				}
				else if(data.code == 0){

					getMoney(data.money);
				}else if (data.code==-8) {
					//alert(data.code);
		     		if(data.status == 0){
		     			emptyMoney();
		     		}else if(data.status == 1){
		     			getMoney(data.money);
		     		}else{
		     			setText('服务器繁忙，请稍后再试','')
		     			//alert('服务器繁忙，请稍后再试');
		     		}
				}else{

					//alert(data.message);
					setText('服务器繁忙，请稍后再试','')
					//alert('服务器繁忙，请稍后再试')
				}
			},'json');
		})
	}
	
	function hideLoad(){
		$('#_pageload').fadeOut();
	}
	
	function setText(text1,text2){
		$('#_page').append(
			'<div class="userinfo">' +
				'<dl>' +
					'<dt><img src="' + logoUrl + '" /></dt>' +
					'<dd>' +
						'<p class="t">' + text1 + '</p>' +
						'<p>'+ text2 +'</p>' +
					'</dd>' +
				'</dl>' +
			'</div>'
    	);
	}

	function emptyMoney(){
    	$('#_page').append(
			'<div class="userinfo">' +
				'<dl>' +
					'<dt><img src="' + logoUrl + '" /></dt>' +
					'<dd>' +
						'<p class="t">您打开了红包，里面空空如也！</p>' +
						'<p>请周五再来</p>' +
					'</dd>' +
				'</dl>' +
			'</div>'
    	);
	}
	
	function getMoney(money){
    	$('#_page').append(
			'<div class="userinfo">' +
				'<dl>' +
					'<dt><img src="' + logoUrl + '" /></dt>' +
					'<dd>' +
						'<p class="t">来自晨报君的红包</p>' +
						//'<p>热烈祝贺晨报官微升级完成</p>' +
					'</dd>' +
				'</dl>' +
			'</div>' +
			
			'<div class="money">' +
				'<dl>' +
				'<dt><b>' + money + '</b>元</dt>' +
				'<dd><p>已存入领钱，可用于发红包</p></dd>' +
				'</dl>' +
			'</div>'
    	);
	}
	
	function init(){
		$.post(isUserinfoUrl,{},function(data){
	     	if(data.code == -1){
	     		setText(data.message,'');
	     	}else if(data.code == -2){
	     		setText(data.message,'请关注我们的后续活动');
	     	}else if (data.code == -3) {
	     		if(data.status == 0){
	     			emptyMoney();
	     		}else if(data.status == 1){
	     			getMoney(data.money);
	     		}else{
	     			alert('服务器繁忙，请稍后再试');
	     		}
	     	}else if (data.code == 1) {
	     		$('#_luckPage').css('display','flex');
	     		draw();
	     	}else{
	     		setText('服务器繁忙，请稍后再试','')
	     		//alert('服务器繁忙，请稍后再试');
	     	}
	     	hideLoad();
	    },'json');
	}
	$(function(){
		isfull();
		init();
	});
	
});