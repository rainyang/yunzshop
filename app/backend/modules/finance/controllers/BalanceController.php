<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/30
 * Time: 下午3:56
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;

class BalanceController extends BaseController
{
    //余额基础设置页面
    public function index()
    {
        $balance = array(
            'transfer' => 0,
            'recharge' => 0,
            'withdraw' => array('status' => 0)
        );
        return view('finance.balance.index', [
            'balance' => $balance,
            'pager' => ''
        ])->render();
    }

    //保存设置
    public function save()
    {
        echo '<pre>'; print_r(\YunShop::request()->balance); exit;
    }

}
