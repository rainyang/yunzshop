<?php
namespace app\frontend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;

class MemberCouponController extends BaseController
{
    //优惠券对于该用户是否可用
    const NOT_AVAILABLE = 1;
    const IS_AVAILABLE = 2;

    //优惠券的状态
    const NOT_USED = 1;
    const OVERDUE = 2;
    const IS_USED = 3;
    const EXHAUST = 4;
    const ALREADY_GOT = 5;
    const ALREADY_GOT_AND_TOUCH_LIMIT = 6;

    /**
     * 获取用户所拥有的优惠券的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsOfMember()
    {
        $uid = \YunShop::app()->getMemberId();
        $pageSize = \YunShop::request()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;

        $coupons = MemberCoupon::getCouponsOfMember($uid)->paginate($pageSize)->toArray();
        if (empty($coupons['data'])){
            return $this->errorJson('没有找到记录', []);
        }

        //给优惠券增加 "是否可用" & "是否已经使用" & "是否过期" 的标识
        $now = strtotime('now');
        foreach($coupons['data'] as $k=>$v){
            if ($v['used'] == MemberCoupon::USED){ //已使用
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::IS_USED;
            } elseif ($v['used'] == MemberCoupon::NOT_USED){ //未使用
                if($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT){ //时间限制类型是"领取后几天有效"
                    if (($now - $v['get_time']) < ($v['belongs_to_coupon']['time_days']*3600)){ //优惠券在有效期内
                        $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE; //可用时, 就没有api_status描述
                    } else{ //优惠券在有效期外
                        $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                        $coupons['data'][$k]['api_status'] = self::OVERDUE;
                    }
                } elseif($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT){ //时间限制类型是"时间范围"
                    if (($now > $v['belongs_to_coupon']['time_end'])){ //优惠券在有效期外
                        $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                        $coupons['data'][$k]['api_status'] = self::OVERDUE;
                    } else{ //优惠券在有效期内
                        $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
                    }
                }
            } else{
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }
        }
        return $this->successJson('ok', $coupons);
    }

    /**
     * 提供给用户的"优惠券中心"的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsForMember()
    {
        $pageSize = \YunShop::request()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;
        $uid = \YunShop::app()->getMemberId();

        $coupons = Coupon::getCouponsForMember($uid)->paginate($pageSize)->toArray();
        if(empty($coupons)){
            return $this->errorJson('没有找到记录', []);
        }

        //增加"是否可领取" & "是否已抢光" & "是否已领取" & "领取数量是否达到个人上限"的标识
        $now = strtotime('now');
        foreach($coupons['data'] as $k=>$v){
            if($v['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT && ($now > $v['time_end'])){ //优惠券已过期
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::OVERDUE;
            } elseif($v['has_many_member_coupon_count'] >= $v['total']){ //优惠券已抢光
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::EXHAUST;
            } elseif($v['member_got_count'] >= $v['get_max']){ //达到个人可领取的上限
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::ALREADY_GOT_AND_TOUCH_LIMIT;
            } elseif($v['member_got_count'] > 0){ //已领取,但没有达到个人可领取的上限
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::ALREADY_GOT;
            } else{
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }
        }

        return $this->successJson('ok', $coupons);
    }

    //获取用户所拥有的不同状态的优惠券 - 待使用(NOT_USED) & 已过期(OVERDUE) & 已使用(IS_USED)
    public function couponsOfMemberByStatus()
    {
        $status = \YunShop::request()->get('status_request');
//        $uid = \YunShop::app()->getMemberId();
        $uid = 140;
        $pageSize = \YunShop::request()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;

        $now = strtotime('now');

        switch ($status) {
            case self::IS_USED:
                $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 1)->paginate($pageSize)->toArray();
                break;
            case self::OVERDUE:
                $coupons = self::getOverdueCoupons($uid, $now, $pageSize);
                break;
            case self::NOT_USED:
                $coupons = self::getAvailableCoupons($uid, $now, $pageSize);
        }

        if (empty($coupons)){
            return $this->errorJson('没有找到记录', []);
        } else{
            return $this->successJson('ok', $coupons);
        }
    }

    //用户所拥有的已过期的优惠券
    public static function getOverdueCoupons($uid, $time, $pageSize=10)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 0)->paginate($pageSize)->toArray();

        $overdueCoupons = array();
        //获取已经过期的优惠券
        foreach($coupons['data'] as $k=>$v){
            if($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT
                && ($time - $v['get_time']) > ($v['belongs_to_coupon']['time_days']*3600) ){ //时间限制类型是"领取后几天有效",且过期
                $overdueCoupons[] = $coupons['data'][$k];
            } elseif($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT
                && ($time > $v['belongs_to_coupon']['time_end'])){
                $overdueCoupons[] = $coupons['data'][$k];
            }
        }
        $coupons['data'] = $overdueCoupons;
        return $coupons;
    }

    //用户所拥有的可使用的优惠券
    public static function getAvailableCoupons($uid, $time, $pageSize=10)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 0)->paginate($pageSize)->toArray();

        //获取可以使用的优惠券
        $availableCoupons = array();
        foreach($coupons['data'] as $k=>$v){
            if($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT
                && (($time - $v['get_time']) < $v['belongs_to_coupon']['time_days']*3600)){ //时间限制类型是"领取后几天有效",且过期
                $availableCoupons[] = $coupons['data'][$k];
            } elseif($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT
                && ($time < $v['belongs_to_coupon']['time_end'])){
                $availableCoupons[] = $coupons['data'][$k];
            }
        }
        $coupons['data'] = $availableCoupons;
        return $coupons;
    }

    //领取优惠券
    public function getCoupon($couponId)
    {

    }

}

