
requirejs.config({
    baseUrl: sourceUrl,
    urlArgs: 'bust=' + (new Date()).getTime(),
    waitSeconds: 0,
    paths: {
        jquery: 'js/jquery',  //jquery库
        $: 'js/jquery',  //jquery库
        swiper: 'js/swiper.min',  //swiper插件
        mock: 'js/mockajax',
        hover3d: 'js/hover3d',  //指向翻转
        imgpreload: 'js/jquery.imgpreload.min',  //图片加载动画
        poptrox: 'js/poptrox', //视频图片弹窗
        accordion: 'js/accordion', //手风琴
        fancybox: 'js/fancybox.min', //fancybox插件
        validate: 'js/validate',
        lazyload: 'js/lazyload', //懒加载
        scrollfix: 'js/scrollfix' //跟随滚动条

    },
    shim: {
        pagefull: {
            deps: ['jquery'],
            exports: 'pagefull'
        },
        mock: {
            deps: ['jquery'],
            mock: 'mock'
        },
        hover3d: {
            deps: ['jquery'],
            exports: 'hover3d'
        },
        imgpreload: {
            deps: ['jquery'],
            exports: 'imgpreload'
        },
        pingzi_video: {
            deps: ['jquery'],
            exports: 'pingzi_video'
        },
        poptrox: {
            deps: ['jquery'],
            exports: 'poptrox'
        },
        accordion: {
            deps: ['jquery'],
            exports: 'accordion'
        },
        fancybox: {
            deps: ['jquery'],
            exports: 'fancybox'
        },
        validate: {
            deps: ['jquery'],
            exports: 'validate'
        },
        lazyload: {
            deps: ['jquery'],
            exports: 'lazyload'
        },
        scrollfix: {
            deps: ['jquery'],
            exports: 'scrollfix'
        }
    }
});