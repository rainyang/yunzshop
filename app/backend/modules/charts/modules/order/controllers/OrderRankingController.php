<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/17 下午3:20
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\order\controllers;


use app\common\components\BaseController;

class OrderRankingController extends BaseController
{

    public function index()
    {
        //dd(123);
        return view('charts.order.order_ranking')->render();
    }


}
