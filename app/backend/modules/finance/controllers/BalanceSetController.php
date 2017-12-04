<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/12/4 下午2:11
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;

class BalanceSetController extends BaseController
{
    public function see()
    {
        return view('finance.balance.index', [
            'balance' => $balance,
        ])->render();
    }

    public function store()
    {

    }


    private function getPostValue()
    {
        return \YunShop::request()->balance;
    }
}
