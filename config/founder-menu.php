<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/21 下午5:01
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

return [
    'founder_plugins'       => [
        'name'          => '插件管理',
        'url'           => 'plugins.get-plugin-data',
        'url_params'    => '',
        'permit'        => 1,
        'menu'          => 1,
        'icon'          => 'fa-puzzle-piece',
        'sort'          => '0',
        'item'          => 'plugins',
        'parents'       => ['system',],
        'child'         => [
            'plugins_enable'    => [
                'name'          => '启用插件',
                'url'           => 'plugins.enable',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 0,
                'icon'          => '',
                'sort'          => '1',
                'item'          => 'plugins_enable',
                'parents'       => ['system', 'plugins',],
            ],

            'plugins_disable'   => [
                'name'          => '禁用插件',
                'url'           => 'plugins.disable',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 0,
                'icon'          => '',
                'sort'          => '2',
                'item'          => 'plugins_disable',
                'parents' => ['system', 'plugins',],
            ],

            'plugins_manage'    => [
                'name'          => '插件安装',
                'url'           => 'plugins.manage',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 0,
                'icon'          => '',
                'sort'          => '3',
                'item'          => 'plugins_manage',
                'parents'       => ['system', 'plugins',],
            ],

            'plugins_delete'    => [
                'name'          => '插件卸载',
                'url'           => 'plugins.delete',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 0,
                'icon'          => '',
                'sort'          => '4',
                'item'          => 'plugins_delete',
                'parents'       => ['system', 'plugins',],
            ],

            'plugins_update'    => [
                'name'          => '插件升级',
                'url'           => 'plugins.update',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 0,
                'icon'          => '',
                'sort'          => '5',
                'item'          => 'plugins_update',
                'parents' => ['system', 'plugins',],
            ],
        ],
    ],

    'shop_upgrade'      => [
        'name'              => '系统升级',
        'url'               => 'update.index',
        'url_params'        => '',
        'permit'            => 1,
        'menu'              => 1,
        'icon'              => 'fa-history',
        'sort'              => '5',
        'item'              => 'shop_upgrade',
        'parents'           => ['system',],
    ]
];
