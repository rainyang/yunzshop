<?php

return [
    'index' => [
        'name' => '控制面板',
        'url' => 'index.index',         // url 可以填写http 也可以直接写路由
        'urlParams' => '',              //如果是url填写的是路由则启用参数否则不启用
        'permit' => 0,      //如果不设置则不会做权限检测
        'menu' => 0,        //如果不设置则不显示菜单，子菜单也将不显示
        'icon' => '',       //菜单图标
        'parents'=>[],
        'child' => []
        ]
];

