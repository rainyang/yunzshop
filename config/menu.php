<?php
/**
 * 增加路由白名单，（控制面板 404 问题）
 *
 * @Author YiTian
 * @Date 2017-06-07
 */
return [
    'index' => [
        'name' => '控制面板',
        'url' => 'index.index',         // url 可以填写http 也可以直接写路由
        'urlParams' => '',              //如果是url填写的是路由则启用参数否则不启用
        'permit' => 0,                  //如果不设置则不会做权限检测
        'menu' => 0,                    //如果不设置则不显示菜单，子菜单也将不显示
        'icon' => '',                   //菜单图标
        'parents'=>[],
        'child' => []
    ],
    'goods.comment.updated' => [
        'name' => '搜索商品',
        'url' => 'goods.goods.get-search-goods',
        'urlParams' => '',
        'permit' => 0,
        'menu' => 0,
        'icon' => '',
        'parents'=>[],
        'child' => []
    ]


];
http://restructure.mamp.com/web/index.php?c=site&a=entry&m=yun_shop&do=4873&route=goods.comment.createReply