/*!
 *
 * Portamento v1.1.1 - 2011-09-02
 * http://simianstudios.com/portamento
 *
 * Copyright 2011 Kris Noble except where noted.
 *
 * Dual-licensed under the GPLv3 and Apache 2.0 licenses:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
(function ($) {
    $.fn.portamento = function (options) {
        var thisWindow = $(window);
        var thisDocument = $(document);
        $.fn.viewportOffset = function () {
            var win = $(window);
            var offset = $(this).offset();
            return {
                left: offset.left - win.scrollLeft(),
                top: offset.top - win.scrollTop()
            };
        };

        function positionFixedSupported() {
            var container = document.body;
            if (document.createElement && container && container.appendChild && container.removeChild) {
                var el = document.createElement("div");
                if (!el.getBoundingClientRect) {
                    return null;
                }
                el.innerHTML = "x";
                el.style.cssText = "position:fixed;top:100px;";
                container.appendChild(el);
                var originalHeight = container.style.height,
                    originalScrollTop = container.scrollTop;
                container.style.height = "3000px";
                container.scrollTop = 500;
                var elementTop = el.getBoundingClientRect().top;
                container.style.height = originalHeight;
                var isSupported = elementTop === 100;
                container.removeChild(el);
                container.scrollTop = originalScrollTop;
                return isSupported;
            }
            return null;
        }

        function getScrollerWidth() {
            var scr = null;
            var inn = null;
            var wNoScroll = 0;
            var wScroll = 0;
            scr = document.createElement('div');
            scr.style.position = 'absolute';
            scr.style.top = '-1000px';
            scr.style.left = '-1000px';
            scr.style.width = '100px';
            scr.style.height = '50px';
            scr.style.overflow = 'hidden';
            inn = document.createElement('div');
            inn.style.width = '100%';
            inn.style.height = '200px';
            scr.appendChild(inn);
            document.body.appendChild(scr);
            wNoScroll = inn.offsetWidth;
            scr.style.overflow = 'auto';
            wScroll = inn.offsetWidth;
            document.body.removeChild(document.body.lastChild);
            return (wNoScroll - wScroll);
        }
        var opts = $.extend({}, $.fn.portamento.defaults, options);
        var panel = this;
        var wrapper = opts.wrapper;
        var gap = opts.gap;
        var disableWorkaround = opts.disableWorkaround;
        var fullyCapableBrowser = positionFixedSupported();
        if (panel.length != 1) {
            return this;
        }
        if (!fullyCapableBrowser && disableWorkaround) {
            return this;
        }
        panel.wrap('<div id="portamento_container" />');
        var float_container = $('#portamento_container');
        float_container.css({
            'min-height': panel.outerHeight(),
            'width': panel.outerWidth()
        });
        var panelOffset = panel.offset().top;
        var panelMargin = parseFloat(panel.css('marginTop').replace(/auto/, 0));
        var realPanelOffset = panelOffset - panelMargin;
        var topScrollBoundary = realPanelOffset - gap;
        var wrapperPaddingFix = parseFloat(wrapper.css('paddingTop').replace(/auto/, 0));
        var containerMarginFix = parseFloat(float_container.css('marginTop').replace(/auto/, 0));
        var ieFix = 0;
        var isMSIE = 0;
        if (isMSIE) {
            ieFix = getScrollerWidth() + 4;
        }
        thisWindow.bind("scroll.portamento", function () {
            if (thisWindow.height() > panel.outerHeight() && thisWindow.width() >= (thisDocument.width() -
                ieFix)) {
                var y = thisDocument.scrollTop();
                if (y >= (topScrollBoundary)) {
                    if ((panel.innerHeight() - wrapper.viewportOffset().top) - wrapperPaddingFix + gap >=
                        wrapper.height()) {
                        if (panel.hasClass('fixed') || thisWindow.height() >= panel.outerHeight()) {
                            panel.removeClass('fixed');
                            panel.css('top', (wrapper.height() - panel.innerHeight()) + 'px');
                        }
                    } else {
                        panel.addClass('fixed');
                        if (fullyCapableBrowser) {
                            panel.css('top', gap + 'px');
                        } else {
                            panel.clearQueue();
                            panel.css('position', 'absolute').animate({
                                top: (0 - float_container.viewportOffset().top + gap)
                            });
                        }
                    }
                } else {
                    panel.removeClass('fixed');
                    panel.css('top', '0');
                }
            } else {
                panel.removeClass('fixed');
            }
        });
        thisWindow.bind("resize.portamento", function () {
            if (thisWindow.height() <= panel.outerHeight() || thisWindow.width() < thisDocument.width()) {
                if (panel.hasClass('fixed')) {
                    panel.removeClass('fixed');
                    panel.css('top', '0');
                }
            } else {
                thisWindow.trigger('scroll.portamento');
            }
        });
        thisWindow.bind("orientationchange.portamento", function () {
            thisWindow.trigger('resize.portamento');
        });
        thisWindow.trigger('scroll.portamento');
        return this;
    };
    $.fn.portamento.defaults = {
        'wrapper': $('body'),
        'gap': 10,
        'disableWorkaround': false
    };
})(jQuery);
