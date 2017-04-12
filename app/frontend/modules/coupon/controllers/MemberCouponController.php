<?php
namespace app\frontend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\common\helpers\PaginationHelper;


class MemberCouponController extends BaseController
{
    //获取用户所有的优惠券 - 1. 已使用, 2. 已过期(超过起止时间 / 超过领取后有效时间), 3. 其它(即可使用)
    public function couponsOfMember()
    {
//        $uid = \YunShop::request()->get('test_uid'); //临时调试: 路由&test_uid=7
        $uid = \YunShop::app()->getMemberId();
        $pageSize = \YunShop::app()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;

        $coupons = MemberCoupon::getCouponsOfMember($uid)->paginate($pageSize)->toArray();
        if (empty($coupons['data'])){
            return $this->errorJson('没有找到记录', []);
        }

        //给优惠券增加 "是否可用" & "是否过期" 的属性
        $now = strtotime('now');
        foreach($coupons['data'] as $k=>$v){
            if ($v['used'] == MemberCoupon::USED){
                $coupons['data'][$k]['available'] = 0;
            } elseif ($v['used'] == MemberCoupon::NOT_USED){
                if($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT_TYPE){
                    if (($now - $v['get_time']) < ($v['belongs_to_coupon']['time_days']*3600)){
                        $coupons['data'][$k]['overdue'] = 0;
                        $coupons['data'][$k]['available'] = 1;
                    } else{
                        $coupons['data'][$k]['overdue'] = 1;
                        $coupons['data'][$k]['available'] = 0;
                    }
                } elseif($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT_TYPE){
                    if (($now > $v['belongs_to_coupon']['time_end'])){
                        $coupons['data'][$k]['overdue'] = 1;
                        $coupons['data'][$k]['available'] = 0;
                    } else{
                        $coupons['data'][$k]['overdue'] = 0;
                        $coupons['data'][$k]['available'] = 1;
                    }
                }
            } else{
                $coupons['data'][$k]['available'] = 1;
            }
        }
        return $this->successJson('ok', $coupons);
    }

    //提供给用户"优惠券中心"的数据
    public function couponsForMember()
    {
        $coupons = Coupon::getCoupons();

        return $this->successJson('ok', $coupons);
    }
}

