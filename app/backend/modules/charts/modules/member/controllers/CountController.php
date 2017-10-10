<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/10 上午10:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\common\components\BaseController;

class CountController extends BaseController
{

    public function index()
    {
        return view('charts.member.index')->render();
    }

}
