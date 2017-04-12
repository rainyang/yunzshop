<?php
namespace app\frontend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;

class MemberCouponController extends BaseController
{
    const NOT_AVAILABLE = 1;
    const IS_AVAILABLE = 2;
    const OVERDUE = 1;
    const EXHAUST = 2;
    const ALREADY_GOT = 3;
    const ALREADY_GOT_AND_TOUCH_LIMIT = 4;
    const IS_USED = 5;

    /**
     * 获取用户所有的优惠券的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsOfMember()
    {
        $uid = \YunShop::app()->getMemberId();
        $pageSize = \YunShop::app()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;

        $coupons = MemberCoupon::getCouponsOfMember($uid)->paginate($pageSize)->toArray();
        if (empty($coupons['data'])){
            return $this->errorJson('没有找到记录', []);
        }

        //给优惠券增加 "是否可用" & "是否已经使用" & "是否过期" 的标识
        $now = strtotime('now');
        foreach($coupons['data'] as $k=>$v){
            if ($v['used'] == MemberCoupon::USED){ //已使用
                $coupons['data'][$k]['availability_dec'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['status_dec'] = self::IS_USED;
            } elseif ($v['used'] == MemberCoupon::NOT_USED){ //未使用
                if($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT_TYPE){ //时间限制类型是"领取后几天有效"
                    if (($now - $v['get_time']) < ($v['belongs_to_coupon']['time_days']*3600)){ //优惠券在有效期内
                        $coupons['data'][$k]['availability_dec'] = self::IS_AVAILABLE; //可用时, 就没有status_dec描述
                    } else{ //优惠券在有效期外
                        $coupons['data'][$k]['availability_dec'] = self::NOT_AVAILABLE;
                        $coupons['data'][$k]['status_dec'] = self::OVERDUE;
                    }
                } elseif($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT_TYPE){ //时间限制类型是"时间范围"
                    if (($now > $v['belongs_to_coupon']['time_end'])){ //优惠券在有效期外
                        $coupons['data'][$k]['availability'] = self::NOT_AVAILABLE;
                        $coupons['data'][$k]['status_dec'] = self::OVERDUE;
                    } else{ //优惠券在有效期内
                        $coupons['data'][$k]['availability_dec'] = self::IS_AVAILABLE;
                    }
                }
            } else{
                $coupons['data'][$k]['availability_dec'] = self::IS_AVAILABLE;
            }
        }
        return $this->successJson('ok', $coupons);
    }

    /**
     * 提供给用户"优惠券中心"的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsForMember()
    {
        $pageSize = \YunShop::app()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;
        $uid = \YunShop::app()->getMemberId();

        $coupons = Coupon::getCouponsForMember($uid)->paginate($pageSize)->toArray();
        if(empty($coupons)){
            return $this->errorJson('没有找到记录', []);
        }

        //增加"是否可领取" & "是否已抢光" & "是否已领取" & "领取数量是否达到个人上限"的标识
        $now = strtotime('now');
        foreach($coupons['data'] as $k=>$v){
            if($v['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT_TYPE && ($now > $v['time_end'])){ //优惠券已过期
                $coupons['data'][$k]['availability_dec'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['status_dec'] = self::OVERDUE;
            } elseif($v['has_many_member_coupon_count'] >= $v['total']){ //优惠券已抢光
                $coupons['data'][$k]['availability_dec'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['status_dec'] = self::EXHAUST;
            } elseif($v['member_got_count'] >= $v['get_max']){ //达到个人可领取的上限
                $coupons['data'][$k]['availability_dec'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['status_dec'] = self::ALREADY_GOT_AND_TOUCH_LIMIT;
            } elseif($v['member_got_count'] > 0){ //已领取,但没有达到个人可领取的上限
                $coupons['data'][$k]['availability_dec'] = self::IS_AVAILABLE;
                $coupons['data'][$k]['status_dec'] = self::ALREADY_GOT;
            } else{
                $coupons['data'][$k]['availability_dec'] = self::IS_AVAILABLE;
            }
        }

        return $this->successJson('ok', $coupons);
    }
}

