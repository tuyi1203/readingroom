/**
 * 根据浏览器宽度及最佳设计效果宽度，自动设置HTML标准字体大小
 * 使用该JS，直接将PS设计的px为单位的数值除以100，即为该JS支持的最佳rem单位，例如字体大小12px 除以100得到 0.12rem，以此类推
 * 使用时，请根据设计稿的支持的最佳尺寸宽度，修改designWith值
 * Created by 刘星 on 2016/9/7.
 */
(function (doc, win) {
		// 最佳效果设计宽
		var designWith = 750;
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            if(!clientWidth) return;
            if (clientWidth >= designWith) {
                docEl.style.fontSize = '50px';
            } else {
                docEl.style.fontSize = 50 * (clientWidth / (designWith / 2)) + 'px';
            }
        };
    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);
