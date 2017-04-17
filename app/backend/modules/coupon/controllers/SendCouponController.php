<?php

namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;

class SendCouponController extends BaseController
{
    //发放优惠券
    public function index()
    {
//        echo(33);exit;
        return view('coupon.send', [
//            'coupon' => $coupon,
        ])->render();
    }
}