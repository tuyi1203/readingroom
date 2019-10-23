/**
 * 晨报发红包
 * 
 * @author kmoon
 * @version1.0 2016-12-24
 */

//设置是否全屏
define(["jquery"],function($){
	isFull = false ;
	
	function isfull(){
		if(isFull == true){
			document.addEventListener('touchmove', function(event) {
				event.preventDefault();
			}, false);
			$('html,body').css({'height':'100%','width':'100%','overflow':'hidden'});
		}
	}
	
	$(function(){
		isfull();
	});
	
});