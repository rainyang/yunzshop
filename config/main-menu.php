<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/21 上午10:51
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

return [
    'index' => [
        'name' => '控制面板',
        'url' => 'index.index',         // url 可以填写http 也可以直接写路由
        'urlParams' => '',              //如果是url填写的是路由则启用参数否则不启用
        'permit' => 0,                  //如果不设置则不会做权限检测
        'menu' => 0,                    //如果不设置则不显示菜单，子菜单也将不显示
        'icon' => '',                   //菜单图标
        'parents' => [],                //
        'child' => [],
    ],
    'system' => [
        'id' => 1,
        'name' => '系统管理',
        'url' => '',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-cogs',
        'parent_id' => 0,
        'sort' => 1,
        'item' => 'system',
        'parents' => [],
        'child' => [
            'Setting' => [
                'id' => '2',
                'name' => '商城设置',
                'url' => 'setting.shop.shop',
                'url_params' => '',
                'permit' => 0,
                'menu' => 1,
                'icon' => 'fa-cog',
                'parent_id' => 1,
                'sort' => 0,
                'item' => 'Setting',
                'parents' => ['system'],
                'child' => [
                    'setting_shop' => [
                        'id' => '21',
                        'name' => '基础设置',
                        'url' => 'setting.shop.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '2',
                        'sort' => 0,
                        'item' => 'setting_shop',
                        'parents' => ['system', 'Setting'],
                        'child' => [
                            'setting_shop_index' => [
                                'id' => '145',
                                'name' => '查看设置',
                                'url' => 'setting.shop.index',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-eye',
                                'parent_id' => '21',
                                'sort' => 1,
                                'item' => 'setting_shop_index',
                                'parents' => ['system', 'Setting', 'setting_shop'],

                            ],
                            'setting_shop_submit' => [
                                'id' => '1160',
                                'name' => '编辑保存',
                                'url' => '',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-floppy-o',
                                'parent_id' => '21',
                                'sort' => '2',
                                'item' => 'setting_shop_submit',
                                'parents' => ['system', 'Setting', 'setting_shop'],
                            ],
                            'setting_member' => [
                                'id' => '128',
                                'name' => '会员设置',
                                'url' => 'setting.shop.member',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-puzzle-piece',
                                'parent_id' => '21',
                                'sort' => '3',
                                'item' => 'setting_member',
                                'parents' => ['system', 'Setting', 'setting_shop'],
                                'child' => [
                                    'setting_member_index' => [
                                        'id' => '146',
                                        'name' => '查看设置',
                                        'url' => 'setting.shop.index',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-eye',
                                        'parent_id' => '128',
                                        'sort' => 1,
                                        'item' => 'setting_member_index',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_member'],
                                    ],
                                    'setting_member_submit' => [
                                        'id' => '1119',
                                        'name' => '编辑保存',
                                        'url' => 'setting.shop.member',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-floppy-o',
                                        'parent_id' => '128',
                                        'sort' => '2',
                                        'item' => 'setting_member_submit',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_member'],
                                    ],
                                ],
                            ],
                            'setting_category' => [
                                'id' => '129',
                                'name' => '分类层级',
                                'url' => 'setting.shop.category',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-puzzle-piece',
                                'parent_id' => '21',
                                'sort' => '4',
                                'item' => 'setting_category',
                                'parents' => ['system', 'Setting', 'setting_shop',],
                                'child' => [
                                    'setting_category_index' => [
                                        'id' => '147',
                                        'name' => '查看设置',
                                        'url' => 'setting.shop.category',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-server',
                                        'parent_id' => '129',
                                        'sort' => 0,
                                        'item' => 'setting_category_index',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_category'],
                                    ],

                                    'setting_category_submit' => [
                                        'id' => '1120',
                                        'name' => '提交设置',
                                        'url' => 'setting_category',
                                        'url_params' => '编辑保存',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-floppy-o',
                                        'parent_id' => '129',
                                        'sort' => '2',
                                        'item' => 'setting_category_submit',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_category',],
                                    ],

                                ],
                            ],
                            'setting_contact' => [
                                'id' => '130',
                                'name' => '联系方式',
                                'url' => 'setting.shop.contact',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-puzzle-piece',
                                'parent_id' => '21',
                                'sort' => '5',
                                'item' => 'setting_contact',
                                'parents' => ['system', 'Setting', 'setting_shop'],
                                'child' => [
                                    'setting_contact_index' => [
                                        'id' => '1121',
                                        'name' => '查看设置',
                                        'url' => 'setting.shop.contact',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-eye',
                                        'parent_id' => '130',
                                        'sort' => 1,
                                        'item' => 'setting_contact_index',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_contact'],
                                    ],
                                    'setting_contact_submit' => [
                                        'id' => '1122',
                                        'name' => '提交设置',
                                        'url' => 'setting_shop_contact',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '130',
                                        'sort' => '3',
                                        'item' => 'setting_contact_submit',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_contact'],
                                    ],
                                ],
                            ],
                            'setting_sms' => [
                                'id' => '131',
                                'name' => '短信设置',
                                'url' => 'setting.shop.sms',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-puzzle-piece',
                                'parent_id' => '21',
                                'sort' => '6',
                                'item' => 'setting_sms',
                                'parents' => ['system', 'Setting', 'setting_shop'],
                                'child' => [
                                    'setting_sms_index' => [
                                        'id' => '1123',
                                        'name' => '查看设置',
                                        'url' => 'setting.shop.sms',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '131',
                                        'sort' => '4',
                                        'item' => 'setting_sms_index',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_sms',],
                                    ],
                                    'setting_sms_submit' => [
                                        'id' => '1124',
                                        'name' => '提交设置',
                                        'url' => 'setting.shop.sms',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '131',
                                        'sort' => '5',
                                        'item' => 'setting_sms_submit',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_sms'],
                                    ],
                                ],
                            ],
                            'setting_slide' => [
                                'id' => '132',
                                'name' => '幻灯片',
                                'url' => 'setting.slide',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-puzzle-piece',
                                'parent_id' => '21',
                                'sort' => '7',
                                'item' => 'setting_slide',
                                'parents' => ['system', 'Setting', 'setting_shop'],
                                'child' => [
                                    'setting_slide_submit' => [
                                        'id' => '1125',
                                        'name' => '提交设置',
                                        'url' => 'setting.slide',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '132',
                                        'sort' => '6',
                                        'item' => 'setting_slide_submit',
                                        'parents' => ['system', 'Setting', 'setting_shop', 'setting_slide'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'setting_shop_trade' => [
                        'id' => '24',
                        'name' => '交易设置',
                        'url' => 'setting.shop.trade',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-sliders',
                        'parent_id' => '2',
                        'sort' => '3',
                        'item' => 'setting_shop_trade',
                        'parents' =>
                            [
                                'system',
                                'Setting',
                            ],

                        'child' => [
                            'setting_trade_index' => [
                                'id' => '1126',
                                'name' => '查看设置',
                                'url' => '',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '24',
                                'sort' => '7',
                                'item' => 'setting_trade_index',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_trade',
                                    ],

                            ],

                            'setting_trade_submit' => [
                                'id' => '1127',
                                'name' => '提交设置',
                                'url' => '',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '24',
                                'sort' => '8',
                                'item' => 'setting_trade_submit',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_trade',
                                    ],

                            ],

                        ],

                    ],

                    'setting_shop_pay' => [
                        'id' => '25',
                        'name' => '支付方式',
                        'url' => 'setting.shop.pay',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '2',
                        'sort' => '3',
                        'item' => 'setting_shop_pay',
                        'parents' =>
                            [
                                'system',
                                'Setting',
                            ],

                        'child' => [
                            'setting_pay_index' => [
                                'id' => '1128',
                                'name' => '查看设置',
                                'url' => 'setting.shop.pay',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '25',
                                'sort' => '9',
                                'item' => 'setting_pay_index',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_pay',
                                    ],

                            ],

                            'setting_pay_submit' => [
                                'id' => '1129',
                                'name' => '提交设置',
                                'url' => 'setting.shop.pay',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '25',
                                'sort' => '10',
                                'item' => 'setting_pay_submit',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_pay',
                                    ],

                            ],

                        ],

                    ],

                    'setting_shop_share' => [
                        'id' => '22',
                        'name' => '分享引导设置',
                        'url' => 'setting.shop.share',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-sliders',
                        'parent_id' => '2',
                        'sort' => '5',
                        'item' => 'setting_shop_share',
                        'parents' =>
                            [
                                'system',
                                'Setting',
                            ],

                        'child' => [
                            'setting_share_index' => [
                                'id' => '1130',
                                'name' => '查看设置',
                                'url' => 'setting.shop.share',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '22',
                                'sort' => '11',
                                'item' => 'setting_share_index',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_share',
                                    ],

                            ],
                            'setting_share_submit' => [
                                'id' => '1131',
                                'name' => '提交设置',
                                'url' => 'setting.shop.share',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '22',
                                'sort' => '12',
                                'item' => 'setting_share_submit',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_share',
                                    ],

                            ],

                        ],

                    ],

                    'setting_shop_notice' => [
                        'id' => '23',
                        'name' => '消息提醒设置',
                        'url' => 'setting.shop.notice',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '2',
                        'sort' => '6',
                        'item' => 'setting_shop_notice',
                        'parents' =>
                            [
                                'system',
                                'Setting',
                            ],

                        'child' => [
                            'setting_notice_index' => [
                                'id' => '1132',
                                'name' => '查看设置',
                                'url' => 'setting.shop.notice',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-eye',
                                'parent_id' => '23',
                                'sort' => 1,
                                'item' => 'setting_notice_index',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_notice',
                                    ],

                            ],

                            'setting_notice_submit' => [
                                'id' => '1133',
                                'name' => '编辑保存',
                                'url' => 'setting.shop.notice',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-floppy-o',
                                'parent_id' => '23',
                                'sort' => '2',
                                'item' => 'setting_notice_submit',
                                'parents' =>
                                    [
                                        'system',
                                        'Setting',
                                        'setting_shop_notice',
                                    ],

                            ],

                        ],

                    ],

                ],

            ],

            'role' => [
                'id' => '30',
                'name' => '角色管理',
                'url' => 'user.role.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-user',
                'parent_id' => 1,
                'sort' => 0,
                'item' => 'role',
                'parents' =>
                    [
                        'system',
                    ],

                'child' => [
                    'role_store' => [
                        'id' => '31',
                        'name' => '添加角色',
                        'url' => 'user.role.store',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-plus',
                        'parent_id' => '30',
                        'sort' => 0,
                        'item' => 'role_store',
                        'parents' =>
                            [
                                'system',
                                'role',
                            ],

                    ],

                    'role_update' => [
                        'id' => '32',
                        'name' => '修改角色',
                        'url' => 'user.role.update',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-pencil-square-o',
                        'parent_id' => '30',
                        'sort' => 0,
                        'item' => 'role_update',
                        'parents' =>
                            [
                                'system',
                                'role',
                            ],

                    ],

                    'role_destroy' => [
                        'id' => '33',
                        'name' => '删除角色',
                        'url' => 'user.role.destory',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-remove',
                        'parent_id' => '30',
                        'sort' => 0,
                        'item' => 'role_destory',
                        'parents' =>
                            [
                                'system',
                                'role',
                            ],

                    ],

                ],

            ],

            'user' => [
                'id' => '34',
                'name' => '操作员',
                'url' => 'user.user.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-list-ul',
                'parent_id' => 1,
                'sort' => 0,
                'item' => 'user',
                'parents' =>
                    [
                        'system',
                    ],

                'child' => [
                    'user_store' => [
                        'id' => '35',
                        'name' => '添加操作员',
                        'url' => 'user.user.store',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-plus',
                        'parent_id' => '34',
                        'sort' => 0,
                        'item' => 'user_store',
                        'parents' =>
                            [
                                'system',
                                'user',
                            ],

                    ],

                    'user_update' => [
                        'id' => '36',
                        'name' => '修改操作员',
                        'url' => 'user.user.update',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-edit',
                        'parent_id' => '34',
                        'sort' => 0,
                        'item' => 'user_update',
                        'parents' =>
                            [
                                'system',
                                'user',
                            ],

                    ],

                    'user_destroy' => [
                        'id' => '37',
                        'name' => '删除操作员',
                        'url' => 'user.user.destroy',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-remove',
                        'parent_id' => '34',
                        'sort' => 0,
                        'item' => 'user_destroy',
                        'parents' =>
                            [
                                'system',
                                'user',
                            ],

                    ],

                ],

            ],

            'plugins' => [
                'id' => '79',
                'name' => '插件管理',
                'url' => 'plugins.get-plugin-data',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => 1,
                'sort' => 0,
                'item' => 'plugins',
                'parents' =>
                    [
                        'system',
                    ],

                'child' => [
                    'plugins_enable' => [
                        'id' => '1113',
                        'name' => '启用插件',
                        'url' => 'plugins.enable',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-check-circle-o',
                        'parent_id' => '79',
                        'sort' => 1,
                        'item' => 'plugins_enable',
                        'parents' =>
                            [
                                'system',
                                'plugins',
                            ],

                    ],

                    'plugins_disable' => [
                        'id' => '1114',
                        'name' => '禁用插件',
                        'url' => 'plugins.disable',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-power-off',
                        'parent_id' => '79',
                        'sort' => '2',
                        'item' => 'plugins_disable',
                        'parents' =>
                            [
                                'system',
                                'plugins',
                            ],

                    ],

                    'plugins_manage' => [
                        'id' => '1112',
                        'name' => '插件安装',
                        'url' => 'plugins.manage',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-download',
                        'parent_id' => '79',
                        'sort' => '3',
                        'item' => 'plugins_manage',
                        'parents' =>
                            [
                                'system',
                                'plugins',
                            ],

                    ],

                    'plugins_delete' => [
                        'id' => '1115',
                        'name' => '插件卸载',
                        'url' => 'plugins.delete',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-close',
                        'parent_id' => '79',
                        'sort' => '4',
                        'item' => 'plugins_delete',
                        'parents' =>
                            [
                                'system',
                                'plugins',
                            ],

                    ],

                    'plugins_update' => [
                        'id' => '1116',
                        'name' => '插件升级',
                        'url' => 'plugins.update',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-history',
                        'parent_id' => '79',
                        'sort' => '5',
                        'item' => 'plugins_update',
                        'parents' =>
                            [
                                'system',
                                'plugins',
                            ],

                    ],

                ],

            ],

            'shop' => [
                'id' => '106',
                'name' => '商城入口',
                'url' => 'setting.shop.entry',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-hand-o-right',
                'parent_id' => 1,
                'sort' => 0,
                'item' => 'shop',
                'parents' =>
                    [
                        'system',
                    ],

            ],

        ],

    ],

    'Goods' => [
        'id' => '6',
        'name' => '商品管理',
        'url' => '',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-pied-piper',
        'parent_id' => 0,
        'sort' => '2',
        'item' => 'Goods',
        'parents' =>
            [
            ],

        'child' => [
            'goods_goods' => [
                'id' => '27',
                'name' => '商品列表',
                'url' => 'goods.goods.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sliders',
                'parent_id' => '6',
                'sort' => 0,
                'item' => 'goods_goods',
                'parents' =>
                    [
                        'Goods',
                    ],

                'child' => [
                    'goods_search' => [
                        'name' => '搜索商品',
                        'url' => 'goods.goods.get-search-goods',
                        'urlParams' => '',
                        'permit' => 0,
                        'menu' => 0,
                        'icon' => '',
                        'parents'=>[],
                        'child' => []
                    ],
                    'goods_goods_edit' => [
                        'id' => '111',
                        'name' => '编辑商品',
                        'url' => 'goods.goods.edit',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '27',
                        'sort' => 0,
                        'item' => 'goods_goods_edit',
                        'parents' =>
                            [
                                'Goods',
                                'goods_goods',
                            ],

                    ],

                    'goods_goods_index' => [
                        'id' => '1142',
                        'name' => '查看设置',
                        'url' => 'goods.goods.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '27',
                        'sort' => '22',
                        'item' => 'goods_goods_index',
                        'parents' =>
                            [
                                'Goods',
                                'goods_goods',
                            ],

                    ],

                    'goods_goods_display_order' => [
                        'id' => '1143',
                        'name' => '提交排序',
                        'url' => 'goods.goods.displayorder',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle',
                        'parent_id' => '27',
                        'sort' => '23',
                        'item' => 'goods_goods_display_order',
                        'parents' =>
                            [
                                'Goods',
                                'goods_goods',
                            ],

                    ],

                ],

            ],
            'goods_category' => [
                'id' => '11',
                'name' => '商品分类',
                'url' => 'goods.category.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sitemap',
                'parent_id' => '6',
                'sort' => '2',
                'item' => 'goods_category',
                'parents' =>
                    [
                        'Goods',
                    ],

                'child' => [
                    'goods_category_add' => [
                        'id' => '12',
                        'name' => '创建分类',
                        'url' => 'goods.category.add-category',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-plus',
                        'parent_id' => '11',
                        'sort' => 0,
                        'item' => 'goods_category_add',
                        'parents' =>
                            [
                                'Goods',
                                'goods_category',
                            ],

                        'child' => [
                            'goods_category.add-category' => [
                                'id' => '1145',
                                'name' => '查看设置',
                                'url' => 'goods.category.add-category',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '12',
                                'sort' => '25',
                                'item' => 'goods_category.add-category',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_category',
                                        'goods_category_add',
                                    ],

                            ],

                            'goods_category.add-category_submit' => [
                                'id' => '1146',
                                'name' => '提交设置',
                                'url' => 'goods.category.add-category',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => '',
                                'parent_id' => '12',
                                'sort' => '26',
                                'item' => 'goods_category.add-category_submit',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_category',
                                        'goods_category_add',
                                    ],

                            ],

                        ],

                    ],

                    'goods_category_edit' => [
                        'id' => '13',
                        'name' => '修改分类',
                        'url' => 'goods.category.edit-category',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-edit',
                        'parent_id' => '11',
                        'sort' => '2',
                        'item' => 'goods_category_edit',
                        'parents' =>
                            [
                                'Goods',
                                'goods_category',
                            ],

                        'child' => [
                            'goods_category_edit_category_submit' => [
                                'id' => '1147',
                                'name' => '提交设置',
                                'url' => 'goods.category.edit-category',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '13',
                                'sort' => '27',
                                'item' => 'goods_category_edit_category_submit',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_category',
                                        'goods_category_edit',
                                    ],

                            ],

                            'goods_category_edit_category_index' => [
                                'id' => '1148',
                                'name' => '查看设置',
                                'url' => 'goods.category.index',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '13',
                                'sort' => '28',
                                'item' => 'goods_category_edit_category_index',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_category',
                                        'goods_category_edit',
                                    ],

                            ],

                        ],

                    ],

                    'goods_category_delete' => [
                        'id' => '14',
                        'name' => '删除分类',
                        'url' => 'goods.category.deleted-category',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-sliders',
                        'parent_id' => '11',
                        'sort' => '3',
                        'item' => 'goods_category_delete',
                        'parents' =>
                            [
                                'Goods',
                                'goods_category',
                            ],

                    ],

                    'goods_category_index' => [
                        'id' => '1144',
                        'name' => '查看设置',
                        'url' => 'goods.category.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle',
                        'parent_id' => '11',
                        'sort' => '24',
                        'item' => 'goods_category_index',
                        'parents' =>
                            [
                                'Goods',
                                'goods_category',
                            ],

                    ],

                ],

            ],

            'goods_brand' => [
                'id' => '7',
                'name' => '品牌管理',
                'url' => 'goods.brand.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-briefcase',
                'parent_id' => '6',
                'sort' => '3',
                'item' => 'goods_brand',
                'parents' =>
                    [
                        'Goods',
                    ],

                'child' => [
                    'goods_brand_add' => [
                        'id' => '8',
                        'name' => '创建品牌',
                        'url' => 'goods.brand.add',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-plus',
                        'parent_id' => '7',
                        'sort' => 0,
                        'item' => 'goods_brand_add',
                        'parents' =>
                            [
                                'Goods',
                                'goods_brand',
                            ],

                        'child' => [
                            'goods_brand_add_index' => [
                                'id' => '1150',
                                'name' => '查看设置',
                                'url' => 'goods.brand.add',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '8',
                                'sort' => '30',
                                'item' => 'goods_brand_add_index',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_brand',
                                        'goods_brand_add',
                                    ],

                            ],

                            'goods_brand_add_submit' => [
                                'id' => '1151',
                                'name' => '提交设置',
                                'url' => 'goods.brand.add',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '8',
                                'sort' => '31',
                                'item' => 'goods_brand_add_submit',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_brand',
                                        'goods_brand_add',
                                    ],

                            ],

                        ],

                    ],

                    'goods_brand_edit' => [
                        'id' => '9',
                        'name' => '修改品牌',
                        'url' => 'goods.brand.edit',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-edit',
                        'parent_id' => '7',
                        'sort' => '2',
                        'item' => 'goods_brand_edit',
                        'parents' =>
                            [
                                'Goods',
                                'goods_brand',
                            ],

                    ],

                    'goods_brand_delete' => [
                        'id' => '10',
                        'name' => '删除品牌',
                        'url' => 'goods.brand.deleted-brand',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-remove',
                        'parent_id' => '7',
                        'sort' => '3',
                        'item' => 'goods_brand_delete',
                        'parents' =>
                            [
                                'Goods',
                                'goods_brand',
                            ],

                    ],

                    'goods_brand_index' => [
                        'id' => '1149',
                        'name' => '查看设置',
                        'url' => 'goods.brand.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle',
                        'parent_id' => '7',
                        'sort' => '29',
                        'item' => 'goods_brand_index',
                        'parents' =>
                            [
                                'Goods',
                                'goods_brand',
                            ],

                    ],

                ],

            ],

            'goods_dispatch' => [
                'id' => '55',
                'name' => '配送模板',
                'url' => 'goods.dispatch',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sliders',
                'parent_id' => '6',
                'sort' => '4',
                'item' => 'goods_dispatch',
                'parents' =>
                    [
                        'Goods',
                    ],

                'child' => [
                    'goods_dispatch_index' => [
                        'id' => '56',
                        'name' => '模板管理',
                        'url' => 'goods.dispatch.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '55',
                        'sort' => 0,
                        'item' => 'goods_dispatch_index',
                        'parents' =>
                            [
                                'Goods',
                                'goods_dispatch',
                            ],

                        'child' => [
                            'goods_dispatch_append' => [
                                'id' => '133',
                                'name' => '添加模板',
                                'url' => 'goods.dispatch.add',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '56',
                                'sort' => 0,
                                'item' => 'goods_dispatch_append',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_dispatch',
                                        'goods_dispatch_index',
                                    ],

                                'child' => [
                                    'goods_dispatch_add_index' => [
                                        'id' => '1153',
                                        'name' => '查看设置',
                                        'url' => 'goods.dispatch.add',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '133',
                                        'sort' => '33',
                                        'item' => 'goods_dispatch_add_index',
                                        'parents' =>
                                            [
                                                'Goods',
                                                'goods_dispatch',
                                                'goods_dispatch_index',
                                                'goods_dispatch_append',
                                            ],

                                    ],

                                    'goods_dispatch_add_submit' => [
                                        'id' => '1154',
                                        'name' => '提交设置',
                                        'url' => 'goods.dispatch.add',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '133',
                                        'sort' => '34',
                                        'item' => 'goods_dispatch_add_submit',
                                        'parents' =>
                                            [
                                                'Goods',
                                                'goods_dispatch',
                                                'goods_dispatch_index',
                                                'goods_dispatch_append',
                                            ],

                                    ],

                                    'goods_dispatch_return' => [
                                        'id' => '1155',
                                        'name' => '返回列表',
                                        'url' => 'goods.dispatch.index',
                                        'url_params' => '',
                                        'permit' => 1,
                                        'menu' => 0,
                                        'icon' => 'fa-circle',
                                        'parent_id' => '133',
                                        'sort' => '35',
                                        'item' => 'goods_dispatch_return',
                                        'parents' =>
                                            [
                                                'Goods',
                                                'goods_dispatch',
                                                'goods_dispatch_index',
                                                'goods_dispatch_append',
                                            ],

                                    ],

                                ],

                            ],

                            'goods_dispatch_alter' => [
                                'id' => '134',
                                'name' => '修改模板',
                                'url' => 'goods.dispatch.edit',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '56',
                                'sort' => 0,
                                'item' => 'goods_dispatch_alter',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_dispatch',
                                        'goods_dispatch_index',
                                    ],

                            ],

                            'goods_dispatch_delete' => [
                                'id' => '135',
                                'name' => '删除模板',
                                'url' => 'goods.dispatch.delete',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '56',
                                'sort' => 0,
                                'item' => 'goods_dispatch_delete',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_dispatch',
                                        'goods_dispatch_index',
                                    ],

                            ],

                            'goods_dispatch_index_one' => [
                                'id' => '1152',
                                'name' => '查看设置',
                                'url' => 'goods.dispatch.index',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '56',
                                'sort' => '32',
                                'item' => 'goods_dispatch_index_one',
                                'parents' =>
                                    [
                                        'Goods',
                                        'goods_dispatch',
                                        'goods_dispatch_index',
                                    ],

                            ],

                        ],

                    ],

                    'goods_dispatch_add_one' => [
                        'id' => '1156',
                        'name' => '添加模板',
                        'url' => 'goods.dispatch.add',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle',
                        'parent_id' => '55',
                        'sort' => 0,
                        'item' => 'goods_dispatch_add_one',
                        'parents' =>
                            [
                                'Goods',
                                'goods_dispatch',
                            ],

                    ],

                ],

            ],

            'comment' => [
                'id' => '15',
                'name' => '评论管理',
                'url' => 'goods.comment.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-columns',
                'parent_id' => '6',
                'sort' => '5',
                'item' => 'comment',
                'parents' =>
                    [
                        'Goods',
                    ],

                'child' => [
                    'goods_comment_add' => [
                        'id' => '16',
                        'name' => '创建评论',
                        'url' => 'goods.comment.add-comment',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-plus',
                        'parent_id' => '15',
                        'sort' => 0,
                        'item' => 'goods_comment_add',
                        'parents' =>
                            [
                                'Goods',
                                'comment',
                            ],

                        'child' => [
                            'goods_comment_add-comment' => [
                                'id' => '1158',
                                'name' => '查看设置',
                                'url' => 'goods.comment.add.comment',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '16',
                                'sort' => '37',
                                'item' => 'goods_comment_add-comment',
                                'parents' =>
                                    [
                                        'Goods',
                                        'comment',
                                        'goods_comment_add',
                                    ],

                            ],

                            'goods_comment_add-comment_index' => [
                                'id' => '1159',
                                'name' => '提交设置',
                                'url' => 'goods.comment.add-comment',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle',
                                'parent_id' => '16',
                                'sort' => '38',
                                'item' => 'goods_comment_add-comment_index',
                                'parents' =>
                                    [
                                        'Goods',
                                        'comment',
                                        'goods_comment_add',
                                    ],

                            ],

                        ],

                    ],

                    'goods_comment_edit' => [
                        'id' => '17',
                        'name' => '修改评论',
                        'url' => 'goods.comment.updated',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-edit',
                        'parent_id' => '15',
                        'sort' => 0,
                        'item' => 'goods_comment_edit',
                        'parents' =>
                            [
                                'Goods',
                                'comment',
                            ],

                    ],

                    'goods_comment_delete' => [
                        'id' => '18',
                        'name' => '删除评论',
                        'url' => 'goods.comment.deleted',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '15',
                        'sort' => 0,
                        'item' => 'goods_comment_delete',
                        'parents' =>
                            [
                                'Goods',
                                'comment',
                            ],

                    ],

                    'goods_comment_reply' => [
                        'id' => '19',
                        'name' => '回复评论',
                        'url' => 'goods.comment.reply',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '15',
                        'sort' => 0,
                        'item' => 'goods_comment_reply',
                        'parents' =>
                            [
                                'Goods',
                                'comment',
                            ],

                    ],

                    'goods_comment_index' => [
                        'id' => '1157',
                        'name' => '查看设置',
                        'url' => 'goods.comment.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle',
                        'parent_id' => '15',
                        'sort' => '36',
                        'item' => 'goods_comment_index',
                        'parents' =>
                            [
                                'Goods',
                                'comment',
                            ],

                    ],

                ],

            ],

            'coupon' => [
                'id' => '101',
                'name' => '优惠券管理',
                'url' => '',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '6',
                'sort' => '6',
                'item' => 'coupon',
                'parents' =>
                    [
                        'Goods',
                    ],

                'child' => [
                    'coupon_coupon_index' => [
                        'id' => '102',
                        'name' => '优惠券列表',
                        'url' => 'coupon.coupon.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '101',
                        'sort' => 1,
                        'item' => 'coupon_coupon_index',
                        'parents' =>
                            [
                                'Goods',
                                'coupon',
                            ],

                        'child' => [
                            'coupon_coupon_edit' => [
                                'id' => '116',
                                'name' => '编辑优惠券',
                                'url' => 'coupon.coupon.edit',
                                'url_params' => 'id',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '102',
                                'sort' => 0,
                                'item' => 'coupon_coupon_edit',
                                'parents' =>
                                    [
                                        'Goods',
                                        'coupon',
                                        'coupon_coupon_index',
                                    ],

                            ],

                            'coupon_coupon_destroy' => [
                                'id' => '117',
                                'name' => '删除优惠券',
                                'url' => 'coupon.coupon.destory',
                                'url_params' => 'id',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '102',
                                'sort' => 0,
                                'item' => 'coupon_coupon_destory',
                                'parents' =>
                                    [
                                        'Goods',
                                        'coupon',
                                        'coupon_coupon_index',
                                    ],

                            ],

                            'coupon_send_coupon' => [
                                'id' => '136',
                                'name' => '发放优惠券',
                                'url' => 'coupon.send-coupon',
                                'url_params' => '',
                                'permit' => 1,
                                'menu' => 0,
                                'icon' => 'fa-circle-o',
                                'parent_id' => '102',
                                'sort' => 0,
                                'item' => 'coupon_send_coupon',
                                'parents' =>
                                    [
                                        'Goods',
                                        'coupon',
                                        'coupon_coupon_index',
                                    ],

                            ],

                        ],

                    ],

                    'coupon_coupon_create' => [
                        'id' => '103',
                        'name' => '创建优惠券',
                        'url' => 'coupon.coupon.create',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '101',
                        'sort' => '2',
                        'item' => 'coupon_coupon_create',
                        'parents' =>
                            [
                                'Goods',
                                'coupon',
                            ],

                    ],

                    'coupon_coupon_log' => [
                        'id' => '105',
                        'name' => '领取发放记录',
                        'url' => 'coupon.coupon.log',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-users',
                        'parent_id' => '101',
                        'sort' => '3',
                        'item' => 'coupon_coupon_log',
                        'parents' =>
                            [
                                'Goods',
                                'coupon',
                            ],

                    ],

                ],

            ],

        ],

    ],

    'Member' => [
        'id' => '38',
        'name' => '会员管理',
        'url' => '',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-users',
        'parent_id' => 0,
        'sort' => '3',
        'item' => 'Member',
        'parents' =>
            [
            ],

        'child' => [
            'member_all' => [
                'id' => '39',
                'name' => '全部会员',
                'url' => 'member.member.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-users',
                'parent_id' => '38',
                'sort' => 0,
                'item' => 'member_all',
                'parents' =>
                    [
                        'Member',
                    ],

                'child' => [
                    'member_detail' => [
                        'id' => '127',
                        'name' => '会员详情',
                        'url' => 'member.member.detail',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'member_detail',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],

                    'order_list_indent' => [
                        'id' => '137',
                        'name' => '会员订单',
                        'url' => 'order.list',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'order_list_indent',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],

                    'finance_point_recharge' => [
                        'id' => '138',
                        'name' => '积分充值',
                        'url' => 'finance.point-recharge',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'finance_point_recharge',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],
                    'finance_balance' => [
                        'id' => '139',
                        'name' => '余额充值',
                        'url' => 'finance.balance.recharge',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'finance_balance',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],

                    'member_member_agent' => [
                        'id' => '140',
                        'name' => '推广下线',
                        'url' => 'member.member.agent',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'member_member_agent',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],

                    'member_member_agent_blacklist' => [
                        'id' => '141',
                        'name' => '加入黑名单',
                        'url' => 'member.member.agent',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'member_member_agent_blacklist',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],

                    'member_member_delete' => [
                        'id' => '142',
                        'name' => '删除会员',
                        'url' => 'member.member.delete',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '39',
                        'sort' => 0,
                        'item' => 'member_member_delete',
                        'parents' =>
                            [
                                'Member',
                                'member_all',
                            ],

                    ],

                ],

            ],

            'member_level' => [
                'id' => '47',
                'name' => '会员等级',
                'url' => 'member.member-level.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sort-amount-asc',
                'parent_id' => '38',
                'sort' => 0,
                'item' => 'member_level',
                'parents' =>
                    [
                        'Member',
                    ],

                'child' => [
                    'member_member_level_store' => [
                        'id' => '48',
                        'name' => '添加会员等级',
                        'url' => 'member.member-level.store',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-plus',
                        'parent_id' => '47',
                        'sort' => 0,
                        'item' => 'member_member_level_store',
                        'parents' =>
                            [
                                'Member',
                                'member_level',
                            ],

                    ],

                    'member_member_level_update' => [
                        'id' => '49',
                        'name' => '编辑会员等级',
                        'url' => 'member.member-level.update',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-edit',
                        'parent_id' => '47',
                        'sort' => 0,
                        'item' => 'member_member_level_update',
                        'parents' =>
                            [
                                'Member',
                                'member_level',
                            ],

                    ],

                    'member_member_level_destroy' => [
                        'id' => '50',
                        'name' => '删除会员等级',
                        'url' => 'member.member-level.destroy',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-remove',
                        'parent_id' => '47',
                        'sort' => 0,
                        'item' => 'member_member_level_destroy',
                        'parents' =>
                            [
                                'Member',
                                'member_level',
                            ],

                    ],

                ],

            ],

            'member_group' => [
                'id' => '51',
                'name' => '会员分组',
                'url' => 'member.member-group.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sort-alpha-asc',
                'parent_id' => '38',
                'sort' => 0,
                'item' => 'member_group',
                'parents' =>
                    [
                        'Member',
                    ],

                'child' => [
                    'member_member_group_store' => [
                        'id' => '52',
                        'name' => '添加会员分组',
                        'url' => 'member.member-group.store',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-plus',
                        'parent_id' => '51',
                        'sort' => 0,
                        'item' => 'member_member_group_store',
                        'parents' =>
                            [
                                'Member',
                                'member_group',
                            ],

                    ],

                    'member_member_group_update' => [
                        'id' => '53',
                        'name' => '修改会员分组',
                        'url' => 'member.member-group.update',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-pencil-square-o',
                        'parent_id' => '51',
                        'sort' => 0,
                        'item' => 'member_member_group_update',
                        'parents' =>
                            [
                                'Member',
                                'member_group',
                            ],

                    ],

                    'member_member_group_destroy' => [
                        'id' => '54',
                        'name' => '删除会员分组',
                        'url' => 'member.member-group.destroy',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-remove',
                        'parent_id' => '51',
                        'sort' => 0,
                        'item' => 'member_member_group_destroy',
                        'parents' =>
                            [
                                'Member',
                                'member_group',
                            ],

                    ],

                ],

            ],

            'member_relation' => [
                'id' => '100',
                'name' => '会员关系',
                'url' => '',
                'url_params' => '',
                'permit' => 0,
                'menu' => 1,
                'icon' => 'fa-crosshairs',
                'parent_id' => '38',
                'sort' => 0,
                'item' => 'member_relation',
                'parents' =>
                    [
                        'Member',
                    ],

                'child' => [
                    'user_relation' => [
                        'id' => '40',
                        'name' => '会员关系设置',
                        'url' => 'member.member-relation.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '100',
                        'sort' => 0,
                        'item' => 'user_relation',
                        'parents' =>
                            [
                                'Member',
                                'member_relation',
                            ],

                    ],

                    'agent_apply' => [
                        'id' => '78',
                        'name' => '资格申请',
                        'url' => 'member.member-relation.apply',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '100',
                        'sort' => 0,
                        'item' => 'agent_apply',
                        'parents' =>
                            [
                                'Member',
                                'member_relation',
                            ],

                    ],

                    'relation_base' => [
                        'id' => '104',
                        'name' => '基础设置',
                        'url' => 'member.member-relation.base',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '100',
                        'sort' => 0,
                        'item' => 'relation_base',
                        'parents' =>
                            [
                                'Member',
                                'member_relation',
                            ],

                    ],

                ],

            ],

        ],

    ],

    'Order' => [
        'id' => '28',
        'name' => '订单管理',
        'url' => 'order.list',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-shopping-cart',
        'parent_id' => 0,
        'sort' => '4',
        'item' => 'Order',
        'parents' =>
            [
            ],

        'child' => [
            'order_list' => [
                'id' => '29',
                'name' => '全部订单',
                'url' => 'order.list.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sliders',
                'parent_id' => '28',
                'sort' => 0,
                'item' => 'order_list',
                'parents' =>
                    [
                        'Order',
                    ],

                'child' => [
                    'order_list_index' => [
                        'id' => '1117',
                        'name' => '查看详情',
                        'url' => 'order.list.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_list_index',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],

                    ],
                    'order_operation_close' => [
                        'id' => '1117',
                        'name' => '关闭订单',
                        'url' => 'order.operation.Close',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_close',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'order_operation_pay' => [
                        'id' => '1117',
                        'name' => '付款',
                        'url' => 'order.operation.pay',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_pay',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'order_operation_cancel_pay' => [
                        'id' => '1117',
                        'name' => '取消付款',
                        'url' => 'order.operation.cancelPay',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_cancel_pay',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'order_operation_send' => [
                        'id' => '1117',
                        'name' => '发货',
                        'url' => 'order.operation.send',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_send',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'order_operation_cancel_send' => [
                        'id' => '1117',
                        'name' => '取消发货',
                        'url' => 'order.operation.cancelSend',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_cancel_send',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'order_operation_receive' => [
                        'id' => '1117',
                        'name' => '确认收货',
                        'url' => 'order.operation.Receive',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_receive',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'change_order_price_index' => [
                        'id' => '1117',
                        'name' => '订单改价详情',
                        'url' => 'order.changeOrderPrice.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'change_order_price_index',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'order_operation_export' => [
                        'id' => '1117',
                        'name' => '订单导出',
                        'url' => 'order.operation.export',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'order_operation_export',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                    'change_order_price_store' => [
                        'id' => '1117',
                        'name' => '订单改价保存',
                        'url' => 'order.changeOrderPrice.store',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 0,
                        'icon' => 'fa-file-text',
                        'parent_id' => '29',
                        'sort' => 1,
                        'item' => 'change_order_price_store',
                        'parents' =>
                            [
                                'Order',
                                'order_list',
                            ],
                    ],
                ],

            ],

            'order_list_waitPay' => [
                'id' => '86',
                'name' => '待支付订单',
                'url' => 'order.list.waitPay',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => 1,
                'item' => 'order_list_waitPay',
                'parents' =>
                    [
                        'Order',
                    ],

            ],

            'order_list_waitSend' => [
                'id' => '87',
                'name' => '待发货订单',
                'url' => 'order.list.waitSend',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => '2',
                'item' => 'order_list_waitSend',
                'parents' =>
                    [
                        'Order',
                    ],

            ],

            'order_list_waitReceive' => [
                'id' => '88',
                'name' => '待收货订单',
                'url' => 'order.list.waitReceive',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => '3',
                'item' => 'order_list_waitReceive',
                'parents' =>
                    [
                        'Order',
                    ],

            ],

            'order_list_completed' => [
                'id' => '89',
                'name' => '已完成订单',
                'url' => 'order.list.completed',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => '5',
                'item' => 'order_list_completed',
                'parents' =>
                    [
                        'Order',
                    ],

            ],

            'order_list_cancelled' => [
                'id' => '99',
                'name' => '已关闭订单',
                'url' => 'order.list.cancelled',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => '5',
                'item' => 'order_list_cancelled',
                'parents' =>
                    [
                        'Order',
                    ],

            ],

            'refund_list_refund' => [
                'id' => '97',
                'name' => '退换货订单',
                'url' => 'refund.list',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => '6',
                'item' => 'refund_list_refund',
                'parents' =>
                    [
                        'Order',
                    ],

                'child' => [
                    'refund_list_refund_all' => [
                        'id' => '110',
                        'name' => '全部',
                        'url' => 'refund.list.refund',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => 1,
                        'item' => 'refund_list_refund_all',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],

                    ],

                    'refund_list_refundMoney' => [
                        'id' => '107',
                        'name' => '仅退款',
                        'url' => 'refund.list.refundMoney',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '2',
                        'item' => 'refund_list_refundMoney',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],

                    ],

                    'refund_list_returnGoods' => [
                        'id' => '108',
                        'name' => '退货退款',
                        'url' => 'refund.list.returnGoods',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '3',
                        'item' => 'refund_list_returnGoods',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],

                    ],

                    'refund_list_exchangeGoods' => [
                        'id' => '109',
                        'name' => '换货',
                        'url' => 'refund.list.exchangeGoods',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_list_exchangeGoods',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_detail_index' => [
                        'id' => '109',
                        'name' => '售后详情',
                        'url' => 'refund.detail.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_detail_index',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_operation_reject' => [
                        'id' => '109',
                        'name' => '拒绝',
                        'url' => 'refund.operation.reject',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_operation_reject',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_pay_index' => [
                        'id' => '109',
                        'name' => '同意退款',
                        'url' => 'refund.pay.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_pay_index',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_operation_pass' => [
                        'id' => '109',
                        'name' => '同意',
                        'url' => 'refund.operation.pass',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_operation_pass',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_operation_receive_return_goods' => [
                        'id' => '109',
                        'name' => '商家确认收货',
                        'url' => 'refund.operation.receiveReturnGoods',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_operation_receive_return_goods',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_operation_resend' => [
                        'id' => '109',
                        'name' => '商家重新发货',
                        'url' => 'refund.operation.resend',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_operation_resend',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                    'refund_operation_consensus' => [
                        'id' => '109',
                        'name' => '手动退款',
                        'url' => 'refund.operation.consensus',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '97',
                        'sort' => '4',
                        'item' => 'refund_operation_consensus',
                        'parents' =>
                            [
                                'Order',
                                'refund_list_refund',
                            ],
                    ],
                ],

            ],

            'refund_list_refunded' => [
                'id' => '98',
                'name' => '已退款',
                'url' => 'refund.list.refunded',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '28',
                'sort' => '7',
                'item' => 'refund_list_refunded',
                'parents' => ['Order',],
            ],

        ],

    ],

    'finance' => [
        'id' => '58',
        'name' => '财务管理',
        'url' => '',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-rmb',
        'parent_id' => 0,
        'sort' => '5',
        'item' => 'finance',
        'parents' => [],
        'child' => [
            'withdraw' => [
                'id' => '59',
                'name' => '提现设置',
                'url' => 'finance.withdraw.set',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sliders',
                'parent_id' => '58',
                'sort' => 0,
                'item' => 'withdraw',
                'parents' =>
                    [
                        'finance',
                    ],

            ],

            'finance_withdraw' => [
                'id' => '66',
                'name' => '提现记录',
                'url' => 'finance.withdraw',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-sliders',
                'parent_id' => '58',
                'sort' => 0,
                'item' => 'finance_withdraw',
                'parents' =>
                    [
                        'finance',
                    ],
                'child' => [
                    'withdraw_status_wait_audit' => [
                        'id' => '92',
                        'name' => '待审核提现',
                        'url' => 'finance.withdraw.index',
                        'url_params' => "&search[status]=0",
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '66',
                        'sort' => 0,
                        'item' => 'withdraw_status_wait_audit',
                        'parents' =>
                            [
                                'finance',
                                'finance_withdraw',
                            ],

                    ],

                    'withdraw_status_wait_pay' => [
                        'id' => '93',
                        'name' => '待打款提现',
                        'url' => 'finance.withdraw',
                        'url_params' => "&search[status]=1",
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '66',
                        'sort' => 0,
                        'item' => 'withdraw_status_wait_pay',
                        'parents' =>
                            [
                                'finance',
                                'finance_withdraw',
                            ],

                    ],

                    'withdraw_status_pay' => [
                        'id' => '94',
                        'name' => '已打款提现',
                        'url' => 'finance.withdraw',
                        'url_params' => "&search[status]=2",
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '66',
                        'sort' => 0,
                        'item' => 'withdraw_status_pay',
                        'parents' =>
                            [
                                'finance',
                                'finance_withdraw',
                            ],

                    ],

                    'withdraw_status_arrival' => [
                        'id' => '95',
                        'name' => '已到账提现',
                        'url' => 'finance.withdraw',
                        'url_params' => "&search[status]=3",
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '66',
                        'sort' => 0,
                        'item' => 'withdraw_status_arrival',
                        'parents' =>
                            [
                                'finance',
                                'finance_withdraw',
                            ],

                    ],

                    'withdraw_status_invalid' => [
                        'id' => '96',
                        'name' => '无效提现',
                        'url' => 'finance.withdraw',
                        'url_params' => "&search[status]=-1",
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '66',
                        'sort' => 0,
                        'item' => 'withdraw_status_invalid',
                        'parents' =>
                            [
                                'finance',
                                'finance_withdraw',
                            ],

                    ],

                ],

            ],

            'finance_point' => [
                'id' => '81',
                'name' => '积分',
                'url' => '',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '58',
                'sort' => 0,
                'item' => 'finance_point',
                'parents' =>
                    [
                        'finance',
                    ],

                'child' => [
                    'point_set' => [
                        'id' => '82',
                        'name' => '积分基础设置',
                        'url' => 'finance.point-set.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '81',
                        'sort' => 0,
                        'item' => 'point_set',
                        'parents' =>
                            [
                                'finance',
                                'finance_point',
                            ],

                    ],

                    'point_member' => [
                        'id' => '83',
                        'name' => '会员积分',
                        'url' => 'finance.point-member.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '81',
                        'sort' => 0,
                        'item' => 'point_member',
                        'parents' =>
                            [
                                'finance',
                                'finance_point',
                            ],

                    ],

                    'point_log' => [
                        'id' => '84',
                        'name' => '积分明细',
                        'url' => 'finance.point-log.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-circle-o',
                        'parent_id' => '81',
                        'sort' => 0,
                        'item' => 'point_log',
                        'parents' =>
                            [
                                'finance',
                                'finance_point',
                            ],

                    ],

                ],

            ],

            'balance' => [
                'id' => '91',
                'name' => '余额管理',
                'url' => 'balance',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-circle-o',
                'parent_id' => '58',
                'sort' => 0,
                'item' => 'balance',
                'parents' =>
                    [
                        'finance',
                    ],
                'child' => [
                    'balance_set' => [
                        'id' => '60',
                        'name' => '余额基础设置',
                        'url' => 'finance.balance.index',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '91',
                        'sort' => 0,
                        'item' => 'balance_set',
                        'parents' =>
                            [
                                'finance',
                                'balance',
                            ],

                    ],

                    'finance_balance_member' => [
                        'id' => '61',
                        'name' => '用户余额管理',
                        'url' => 'finance.balance.member',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '91',
                        'sort' => 0,
                        'item' => 'finance_balance_member',
                        'parents' =>
                            [
                                'finance',
                                'balance',
                            ],

                    ],

                    'finance_balance_rechargeRecord' => [
                        'id' => '62',
                        'name' => '余额充值记录',
                        'url' => 'finance.balance.rechargeRecord',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '91',
                        'sort' => 0,
                        'item' => 'finance_balance_rechargeRecord',
                        'parents' =>
                            [
                                'finance',
                                'balance',
                            ],

                    ],

                    'finance_balance_tansferRecord' => [
                        'id' => '63',
                        'name' => '余额转让记录',
                        'url' => 'finance.balance.transferRecord',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-sliders',
                        'parent_id' => '91',
                        'sort' => 0,
                        'item' => 'finance_balance_tansferRecord',
                        'parents' =>
                            [
                                'finance',
                                'balance',
                            ],

                    ],

                    'finance_balance_balanceDetail' => [
                        'id' => '85',
                        'name' => '余额明细',
                        'url' => 'finance.balance.balanceDetail',
                        'url_params' => '',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => 'fa-file-text',
                        'parent_id' => '91',
                        'sort' => 0,
                        'item' => 'finance_balance_balanceDetail',
                        'parents' =>
                            [
                                'finance',
                                'balance',
                            ],

                    ],

                ],

            ],

        ],

    ],


];

