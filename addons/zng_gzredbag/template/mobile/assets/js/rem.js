/**
 * �����������ȼ�������Ч����ȣ��Զ�����HTML��׼�����С
 * ʹ�ø�JS��ֱ�ӽ�PS��Ƶ�pxΪ��λ����ֵ����100����Ϊ��JS֧�ֵ����rem��λ�����������С12px ����100�õ� 0.12rem���Դ�����
 * ʹ��ʱ���������Ƹ��֧�ֵ���ѳߴ��ȣ��޸�designWithֵ
 * Created by ���� on 2016/9/7.
 */
(function (doc, win) {
		// ���Ч����ƿ�
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
