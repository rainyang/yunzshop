<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/27
 * Time: 下午4:49
 */

namespace app\backend\modules\balance\controllers;


use app\common\components\BaseController;
use app\common\models\finance\Balance;

class BalanceController extends BaseController
{
    public function test()
    {
        $datas = array();
        for ($i = 1; $i < 100000; $i++) {
            $data = array(
                'uniacid' => rand(1, 100),
                'member_id' => rand(1, 100000),
                'old_money' => rand(1, 100000),
                'change_money' => rand(1, 100000),
                'new_money' => rand(1, 100000),
                'type' => rand(1,2),
                'service_type' =>11
            );

            $datas[] = $data;
        }
        $test = Balance::addData($datas);
        dd($test);
    }
    /*
     * 余额基础设置
     *
     * */
    public function index()
    {
        $balance = array(
            'transfer' => 0,
            'recharge' => 0,
            'withdraw' => array('status' => 0)
        );
        return view('balance.index', [
            'balance' => $balance,
            'pager' => ''
        ])->render();
    }

    /*
     * 保存余额基础设置
     * */
    public function save()
    {
        dd(\YunShop::request());
    }

}
