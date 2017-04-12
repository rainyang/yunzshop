<?php
namespace app\frontend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\common\models\Member;

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
//        $pageSize = \YunShop::app()->get('pagesize');
//        $pageSize = $pageSize ? $pageSize : 10; //todo 分页, 记得数据是在['data']下面
        $uid = \YunShop::app()->getMemberId();
//        $member = Member::getMemberById($uid);

        $coupons = Coupon::getCouponsForMember($uid)->get()->toArray();
        if(empty($coupons)){
            return $this->errorJson('没有找到记录', []);
        }

        //增加"是否可领取 available" & "是否已抢光 exhaust" & "是否已领取 alredyGot" & "领取数量是否达到个人上限 touchLimit"的属性
        $now = strtotime('now');
        foreach($coupons as $k=>$v){
            if($v['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT_TYPE && ($now > $v['time_end'])){ //优惠券已过期
                $coupons[$k]['available'] = 0;
                $coupons[$k]['overdue'] = 1;
            } elseif($v['has_many_member_coupon_count'] >= $v['total']){ //优惠券已抢光
                $coupons[$k]['available'] = 0;
                $coupon[$k]['exhaust'] = 1;
            } elseif($v['member_got_count'] >= $v['get_max']){ //达到个人可领取的上限
                $coupons[$k]['available'] = 0;
                $coupons[$k]['touchLimit'] = 1;
                $coupons[$k]['alredyGot'] = 1;
            } elseif($v['member_got_count'] > 0){
                $coupons[$k]['available'] = 1;
                $coupons[$k]['touchLimit'] = 0;
                $coupons[$k]['alredyGot'] = 1;
            } else{
                $coupons[$k]['available'] = 1;
            }
        }

        return $this->successJson('ok', $coupons);
    }
}

