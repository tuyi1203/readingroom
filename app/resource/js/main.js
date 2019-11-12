
requirejs.config({
    baseUrl: "/app/resource/js",
    urlArgs: 'bust=' + (new Date()).getTime(),
    waitSeconds: 0,
    paths: {
        'jquery': 'jquery',
        $: "jquery",
        script:'script',
        bs:'bootstrap.min',
    },
    shim: {
        pagefull: {
            deps: ['jquery'],
            exports: 'pagefull'
        },
        script: {
        	deps:['jquery'],
        	exports:'script'
        },
        bs: {
            deps:['jquery'],
            export:'bs',
        }
    }
});