(function (doc, win) {
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
