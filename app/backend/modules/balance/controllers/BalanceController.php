<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/27
 * Time: 下午4:49
 */

namespace app\backend\modules\balance\controllers;


use app\common\components\BaseController;

class BalanceController extends BaseController
{
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
