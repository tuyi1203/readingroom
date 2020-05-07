/**
 * 钰盾
 *
 * @version 1.0 2020-1-8
 * @author Martini
 */

define(["jquery", "imgpreload", "validate"], function ($, imgpreload, validate) {
	var windowHt = window.height,
		windowWt = window.width,
		script = {};
	var loaderInterval = 1600;//加载动画间隔
	var loaders = [//加载动画内容
		["<img src='assets/img/logo.png'/>", "加载中，请稍后..."],
	];

	var loader = function (obj) {//加载动画
		var $loaderContainer = $(obj);
		var cycleLoader = function () {
			var index = Math.floor(Math.random() * loaders.length);
			var selected = loaders[index];
			var selectedEmoji = selected[0];
			var selectedText = selected[1];
			// First transition out the old loader
			setTimeout(function () {
				$loaderContainer.children().addClass("animateOut");
			}, loaderInterval - 300);
			$loaderContainer.children(".emoji").last().remove();
			$loaderContainer.children(".text").last().remove();
			// Then animate in the new one
			$loaderContainer.append('<div class="emoji icon">' + selectedEmoji + '</div>');
			$loaderContainer.append('<div class="text">' + selectedText + '</div>');
		}
		setInterval(cycleLoader, loaderInterval);
		cycleLoader();//Run first time without delay
	}
	////调用范例 script.loader('#_loader');

	var preload = function (pageLoad, pageLoader, datalist) {//加载长度执行
		imgNum = 0;
		//console.log(datalist);
		$.imgpreload(datalist, {
			each: function () {
				var status = $(this).data('loaded') ? 'success' : 'error';
				if (status == "success") {
					var v = (parseFloat(++imgNum) / datalist.length).toFixed(2);
				}
			},
			all: function () {
				$(pageLoader).addClass('pageLoadHide');
				$('body').css({ 'height': 'auto', 'overflow-y': 'auto' });
				//console.log('全部加载完成');
			}
		});
	};
	//调用范例 script.preload('#_loader','#_pageLoader',imagesLst);

	var dropList = function (obj, e, m) {
		var $obj = $(obj);
		$obj.bind(e, function () {
			var $this = $(this),
				$parent = $(this).parent();
			$this.addClass('show');
			$this.bind(m, function () {
				$(this).removeClass('show');
			});
		});
	};
	//下拉控件 script.dropList(事件对象,显示时的事件类型,隐藏时的事件类型);
	var backtop = function (obj, offset, duration) {
		var $obj = $(obj),
			$offset = offset,
			$duration = duration,
			offset_opacity = 1200;

		$(window).scroll(function () {
			($(this).scrollTop() > offset) ? $obj.addClass('backtopVisible') : $obj.removeClass('backtopVisible backtopOut');
			if ($(this).scrollTop() > offset_opacity) {
				$obj.addClass('backtopOut');
			}
		});

		$obj.on('click', function (event) {
			event.preventDefault();
			$('body,html').animate({
				scrollTop: 0,
			}, $duration
			);
		});
	};//回到顶部 script.backtop(点击对象,顶部距离显示,滑动速度);

	script.preload = preload;
	script.loader = loader;
	script.backtop = backtop;
	script.dropList = dropList;
	return script;
});
