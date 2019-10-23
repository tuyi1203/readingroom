/**
 * 重庆百君律师事务所
 * 
 * @version 1.0 2018-5-5
 */

define(["jquery"],function (){
	var windowHt = window.height;
	var script = {};

	// var handler = function() {
	// 	event.preventDefault();
	// };
	var fullPage = function(dom){
		var $dom = $(dom);
		$dom.css('height',window.innerHeight);
	};

	var menu = function(link,menu,close){
		var $link = $(link),
			$menu = $(menu),
			$close = $(close),
			mFlag = 0;
		$link.bind('click',function(){
			if(mFlag == 0){
				$menu.css('left','0');
				mFlag = 1;
			}
		});
		$close.bind('click',function(){
			if(mFlag == 1){
				$menu.css('left','100%');
				mFlag = 0;
			}
		})
	};


	var pageLoadHide = function(ele){
		var $ele = $(ele);
		$ele.find('p').text('');
		$ele.removeClass('dialogShow').addClass('dialogClose');
	};
	var pageLoadShow = function(ele,txt){
		var $ele = $(ele);
		$ele.find('p').text(txt);
		$ele.removeClass('dialogClose').addClass('dialogShow');
	};

	var nav = function(ele){
		var $nav = $(ele);
		$nav.find('dl dt').bind('click',function(){
			$this = $(this);
			if($this.parent('dl').hasClass('open') && $this.siblings('dd').length > 0){
				$this.parent('dl').removeClass('open');
			}else{
				$this.parent('dl').siblings('dl').removeClass('open');
				$this.parent('dl').addClass('open');
			}
		})
	}

	script.fullPage = fullPage;
	script.menu = menu;
	script.pageLoadHide = pageLoadHide;
	script.pageLoadShow = pageLoadShow;
	script.nav = nav;
	return script;
});
