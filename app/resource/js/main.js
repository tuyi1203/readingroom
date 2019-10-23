
requirejs.config({
    baseUrl: "/app/resource/js",
    urlArgs: 'bust=' + (new Date()).getTime(),
    waitSeconds: 0,
    paths: {
        'jquery': 'jquery',
        $: "jquery",
        script:'script'
    },
    shim: {
        pagefull: {
            deps: ['jquery'],
            exports: 'pagefull'
        },
        script: {
        	deps:['jquery'],
        	exports:'script'
        }
    }
});