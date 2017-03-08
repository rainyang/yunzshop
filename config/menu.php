<?php

return [
    'system' => [
        'name' => '系统管理',
        'url' => '',// url 可以填写http 也可以直接写路由
        'urlParams' => ['parent_id' => 5],//如果是url填写的是路由则启用参数否则不启用
        'permit' => true,//如果不设置则不会做权限检测
        'menu' => true,//如果不设置则不显示菜单，子菜单也将不显示
        'icon' => '',//菜单图标
        'child' => [
            'menu' => [
                'name' => '菜单管理',
                'permit' => true,
                'menu' => true,
                'icon' => '',
                'url' => 'menu.index',
                'urlParams' => ['parent_id' => 5],
                'child' => [
                    'menu.index' => ['name' => '菜单列表', 'permit' => true,],
                    'menu.add' => ['name' => '新增菜单', 'permit' => true,],
                    'menu.edit' => ['name' => '修改菜单', 'permit' => true,],
                    'menu.sort' => ['name' => '菜单排序', 'permit' => true,],
                    'menu.status' => ['name' => '菜单状态管理', 'permit' => true,],
                    'menu.delete' => ['name' => '删除菜单', 'permit' => true,],
                ]
            ],
            'user' => [
                'name' => '用户管理',
                'permit' => true,
                'menu' => true,
                'icon' => '',
                'url' => 'user.index',
                'child' => [
                    'user.index' => ['name' => '用户列表', 'permit' => true],
                    'user.add' => ['name' => '新增用户', 'permit' => true,],
                    'user.edit' => ['name' => '修改用户', 'permit' => true,],
                    'user.status' => ['name' => '用户状态管理', 'permit' => true,],
                    'user.delete' => ['name' => '删除用户', 'permit' => true,],
                ]
            ],
            'role' => [
                'name' => '角色管理'
            ],
        ]
    ],
    'goods' => [
        'name' => '产品管理',
        'url' => '',// url 可以填写http 也可以直接写路由
        'urlParams' => ['parent_id' => 5],//如果是url填写的是路由则启用参数否则不启用
        'permit' => true,//如果不设置则不会做权限检测
        'menu' => true,//如果不设置则不显示菜单，子菜单也将不显示
        'icon' => '',//菜单图标
        'child' => [
            'brand' => [
                'name' => '品牌管理',
                'url' => 'goods.brand.index',// url 可以填写http 也可以直接写路由
                'urlParams' => [],//如果是url填写的是路由则启用参数否则不启用
                'permit' => true,//如果不设置则不会做权限检测
                'menu' => true,//如果不设置则不显示菜单，子菜单也将不显示
                'icon' => '',//菜单图标
                'child' => [
                    'goods.brand.add' => [
                        'name' => '创建品牌',
                        'url' => '',// url 可以填写http 也可以直接写路由
                        'urlParams' => ['parent_id' => 5],//如果是url填写的是路由则启用参数否则不启用
                        'permit' => true,//如果不设置则不会做权限检测
                        'menu' => false,//如果不设置则不显示菜单，子菜单也将不显示
                        'icon' => '',//菜单图标]
                    ],
                    'goods.brand.edit' => [
                        'name' => '修改品牌',
                        'url' => '',// url 可以填写http 也可以直接写路由
                        'urlParams' => ['parent_id' => 5],//如果是url填写的是路由则启用参数否则不启用
                        'permit' => true,//如果不设置则不会做权限检测
                        'menu' => false,//如果不设置则不显示菜单，子菜单也将不显示
                        'icon' => '',//菜单图标
                    ]
                ]
            ]
        ]
    ]
];