<?php
namespace app\backend\modules\coupon\controllers;
use app\backend\modules\coupon\models\Coupon;

/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/20
 * Time: 16:20
 */
class CouponController extends \app\common\components\BaseController
{
    public function index()
    {
        $coupon = new Coupon();
        return view('coupon.index', [
            'coupon' => $coupon,
            'var' => \YunShop::app()->get(),
        ])->render();
    }

    public function create()
    {
        $coupon = new Coupon();
        return view('coupon.coupon', [
            'coupon' => $coupon,
            'var' => \YunShop::app()->get(),
        ])->render();
    }
}