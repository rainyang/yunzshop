<?php
return array(
    'member' => array(
        'describe' => '管理员',
        'method' => array(
            'login' => array(
                'describe' => '登录',
                'para' => array(
                    'username' => 'admin',
                    'password' => 'admin'
                ),
                'data' => array()
            ))
    ),
    'account' => array(
        'describe' => '公众号',
        'method' => array(
            'display' => array(
                'describe' => '列表',
                'para' => array(
                    'uid' => '1',
                ),
                'data' => array()
            ))
    ),
    'goods' => array(
        'describe' => '商品',
        'method' => array(
            'display' => array(
                'describe' => '列表',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                ),
                'data' => array()
            ),
            'detail' => array(
                'describe' => '详情',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'goods_id' => '3',
                ),
                'data' => array()
            ))
    ),
    'statistics' => array(
        'describe' => '统计',
        'method' => array(
            'sale' => array(
                'describe' => '首页',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                ),
                'data' => array()
            )
        )
    ),
    'order' => array(
        'describe' => '订单',
        'method' => array(
            'display' => array(
                'describe' => '列表',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '0',
                    'status' => '0'
                ),
                'data' => array()
            ),
            'detail' => array(
                'describe' => '详情',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '1',
                ),
                'data' => array()
            ),

            'change_price' => array(
                'describe' => '改价',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '1',
                ),
                'data' => array()
            )
        )
    )
);