<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/30
 * Time: 上午10:42
 */

namespace app\common\repositories;

use Illuminate\Database\Eloquent\Collection;

class ExpressCompany extends Collection
{
    public function __construct($items = [])
    {
        $items = array (
            0 =>
                array (
                    'name' => '申通',
                    'code' => 'shentong',
                ),
            1 =>
                array (
                    'name' => '圆通',
                    'code' => 'yuantong',
                ),
            2 =>
                array (
                    'name' => '中通',
                    'code' => 'zhongtong',
                ),
            3 =>
                array (
                    'name' => '汇通',
                    'code' => 'huitongkuaidi',
                ),
            4 =>
                array (
                    'name' => '韵达',
                    'code' => 'yunda',
                ),
            5 =>
                array (
                    'name' => '顺丰',
                    'code' => 'shunfeng',
                ),
            6 =>
                array (
                    'name' => 'ems',
                    'code' => 'ems',
                ),
            7 =>
                array (
                    'name' => '天天',
                    'code' => 'tiantian',
                ),
            8 =>
                array (
                    'name' => '宅急送',
                    'code' => 'zhaijisong',
                ),
            9 =>
                array (
                    'name' => '邮政',
                    'code' => 'youzhengguonei',
                ),
            10 =>
                array (
                    'name' => '德邦',
                    'code' => 'debangwuliu',
                ),
            11 =>
                array (
                    'name' => '全峰',
                    'code' => 'quanfengkuaidi',
                ),
        );
        parent::__construct($items);
    }

}