<?php
return array(
    'member' => array(
        'describe' => '管理员',
        'method' => array(
            'Login' => array(
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
            'Display' => array(
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
            'Display' => array(
                'describe' => '列表',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                ),
                'data' => array()
            ),
            'Detail' => array(
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
            'Sale' => array(
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
            'Display' => array(
                'describe' => '列表',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '0',
                    'status' => '4'
                ),
                'data' => array()
            ),
            'Express' => array(
                'describe' => '配送信息',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '50',
                ),
                'data' => array()
            ),
            'Detail' => array(
                'describe' => '详情',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '22',
                ),
                'data' => array()
            ),

            'ChangePrice' => array(
                'describe' => '改价',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '21',
                    'changegoodsprice'=>array(
                        '17'=>'-23'
                    ),
                    'changedispatchprice'=>'6'
                ),
                'data' => array()
            ),

            'ChangeStatus' => array(
                'describe' => '改变订单状态',
                'para' => array(
                    'uid' => '1',
                    'uniacid' => '2',
                    'order_id' => '30',
                    'refundstatus' => '2',
                ),
                'data' => array()
            )
        )
    )
);