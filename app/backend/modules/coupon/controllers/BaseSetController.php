<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/25 下午6:38
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\coupon\controllers;


use app\common\components\BaseController;

class BaseSetController extends BaseController
{

    public function index()
    {
        return view('coupon.base_set', [])->render();
    }

}
