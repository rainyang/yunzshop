require.config({
    urlArgs: 'v=201510211501',
    baseUrl: '../addons/sz_yi/static/js/app',
    paths: {
        'jquery': '../dist/jquery-1.11.1.min',
        'jquery.ui': '../dist/jquery-ui-1.10.3.min',
        'bootstrap': '../dist/bootstrap.min',
        'tpl':'../dist/tmodjs',
        'jquery.touchslider':'../dist/jquery.touchslider.min',
        'swipe':'../dist/swipe',
        'Swiper':'../dist/swiper',
        'sweetalert':'../dist/sweetalert/sweetalert.min'
        /*'swiper':'../dist/swiper'*/
        
    },
    shim: {
        'jquery.ui': {
            exports: "$",
            deps: ['jquery']
        },
        'bootstrap': {
            exports: "$",
            deps: ['jquery']
        },  
        'jquery.touchslider': {
            exports: "$",
            deps: ['jquery']
        },
        'sweetalert':{
            exports: "$",
            deps: ['css!../dist/sweetalert/sweetalert.css']
        }

    }
});
