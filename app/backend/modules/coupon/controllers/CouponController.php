<?php
namespace app\backend\modules\coupon\controllers;

use app\backend\modules\coupon\models\Coupon;
use app\common\helpers\PaginationHelper;
use app\common\models\MemberCoupon;

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
        $list = Coupon::uniacid()->paginate(20)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        foreach($list['data'] as &$item){
            $item['gettotal'] = MemberCoupon::uniacid()->where("coupon_id", $item['id'])->count();
            $item['usetotal'] =  MemberCoupon::uniacid()->where("coupon_id", $item['id'])->where("used", 1)->count();
            $item['lasttotal'] = $item['total'] - $item['gettotal'];
        }

        //dd($list);
        return view('coupon.index', [
            'list' => $list['data'],
            'pager' => $pager,
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