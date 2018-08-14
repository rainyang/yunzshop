<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/17 下午12:02
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\goods\controllers;


use app\common\components\BaseController;

class SalesCountController extends BaseController
{

    public function index()
    {
        //dd(123);
        return view('charts.goods.sales_count')->render();
    }



}
