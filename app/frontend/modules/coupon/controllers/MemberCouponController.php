<?php
namespace app\frontend\modules\coupon\controllers;

use app\common\components\ApiController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;

class MemberCouponController extends ApiController
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

        //添加 "是否可用" & "是否已经使用" & "是否过期" 的标识
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

        //添加"是否可领取" & "是否已抢光" & "是否已领取" & "领取数量是否达到个人上限"的标识
        $now = strtotime('now');
        foreach($coupons['data'] as $k=>$v){
            if($v['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT && ($now > $v['time_end'])){ //优惠券已过期
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::OVERDUE;
            } elseif(($v['has_many_member_coupon_count'] >= $v['total']) && ($v['total'] != -1)){ //优惠券已抢光(PS.total=-1是bu限制数量)
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::EXHAUST;
            } elseif(($v['member_got_count'] >= $v['get_max']) && ($v['get_max'] != -1)){ //达到个人可领取的上限
                $coupons['data'][$k]['api_availability'] = self::NOT_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::ALREADY_GOT_AND_TOUCH_LIMIT;
            } elseif($v['member_got_count'] > 0){ //已领取,但没有达到个人可领取的上限
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
                $coupons['data'][$k]['api_status'] = self::ALREADY_GOT;
            } else{
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }

            //添加优惠券使用范围描述
            switch($v['use_type']){
                case 1:
                    $coupons['data'][$k]['api_limit'] = '仅下订单时可用';
                    break;
                case 2:
                    $coupons['data'][$k]['api_limit'] = '商城通用';
                    break;
                case 3:
                    $coupons['data'][$k]['api_limit'] = '适用于下列分类: ';
                    foreach($v['categorynames'] as $sub){
                        $coupons['data'][$k]['api_limit'] .= ' "'.$sub.'"';
                    }
                    break;
                case 4:
                    $coupons['data'][$k]['api_limit'] = '适用于下列商品: ';
                    foreach($v['goods_names'] as $sub){
                        $coupons['data'][$k]['api_limit'] .= ' "'.$sub.'"';
                    }
                    break;
            }
        }

        return $this->successJson('ok', $coupons);
    }

    //获取用户所拥有的不同状态的优惠券 - 待使用(NOT_USED) & 已过期(OVERDUE) & 已使用(IS_USED)
    public function couponsOfMemberByStatus()
    {
        $status = \YunShop::request()->get('status_request');
        $uid = \YunShop::app()->getMemberId();

        $now = strtotime('now');
        switch ($status) {
            case self::NOT_USED:
                $coupons = self::getAvailableCoupons($uid, $now);
                break;
            case self::OVERDUE:
                $coupons = self::getOverdueCoupons($uid, $now);
                break;
            case self::IS_USED:
                $coupons = self::getUsedCoupons($uid);
                break;
        }

        if (empty($coupons)){
            return $this->errorJson('没有找到记录', []);
        } else{
            return $this->successJson('ok', $coupons);
        }
    }

    //用户所拥有的可使用的优惠券
    public static function getAvailableCoupons($uid, $time)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 0)->get()->toArray();

        $availableCoupons = array();
        foreach($coupons as $k=>$v){
            $coupons[$k]['belongs_to_coupon']['deduct'] = intval($coupons[$k]['deduct']); //todo 待优化
            $coupons[$k]['belongs_to_coupon']['discount'] = $coupons[$k]['deduct'] * 10; //todo 待优化
            if(
                ($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT
                    && (($time - $v['get_time']) < $v['belongs_to_coupon']['time_days']*3600)) //时间限制类型是"领取后几天有效",且没过期
                ||
                ($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT
                    && ($time < $v['belongs_to_coupon']['time_end'])) //时间限制类型是"时间范围",且没过期
            ){
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $availableCoupons[] = array_merge($coupons[$k], $usageLimit);
            }
        }
        return $availableCoupons;
    }

    //用户所拥有的已过期的优惠券
    public static function getOverdueCoupons($uid, $time)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 0)->get()->toArray();

        $overdueCoupons = array();
        //获取已经过期的优惠券
        foreach($coupons as $k=>$v){
            $coupons[$k]['belongs_to_coupon']['deduct'] = intval($coupons[$k]['deduct']); //todo 待优化
            $coupons[$k]['belongs_to_coupon']['discount'] = $coupons[$k]['deduct'] * 10; //todo 待优化
            if(
                ($v['belongs_to_coupon']['time_limit'] == Coupon::RELATIVE_TIME_LIMIT
                && ($time - $v['get_time']) > ($v['belongs_to_coupon']['time_days']*3600)) //时间限制类型是"领取后几天有效", 且过期
                ||
                (($v['belongs_to_coupon']['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT
                    && ($time > $v['belongs_to_coupon']['time_end']))) //时间限制类型是"时间范围",且过期
            ){
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $overdueCoupons[] = array_merge($coupons[$k], $usageLimit);
            }
        }
        return $overdueCoupons;
    }

    //用户所拥有的已使用的优惠券
    public static function getUsedCoupons($uid)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 1)->get()->toArray();

        $usedCoupons = array();
        //增加属性 - 优惠券的适用范围
        foreach($coupons as $k=>$v){
            $coupons[$k]['belongs_to_coupon']['deduct'] = intval($coupons[$k]['deduct']); //todo 待优化
            $coupons[$k]['belongs_to_coupon']['discount'] = $coupons[$k]['deduct'] * 10; //todo 待优化
            $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon']));
            $usedCoupons[] = array_merge($coupons[$k], $usageLimit);
        }
        return $usedCoupons;
    }

    /**
     * @param $couponInArrayFormat array
     * @return string 优惠券适用范围的描述
     */
    public static function usageLimitDescription($couponInArrayFormat)
    {
        switch($couponInArrayFormat['use_type']){
            case 1:
                return ('仅下订单时可用');
                break;
            case 2:
                return ('商城通用');
                break;
            case 3:
                $res = '适用于下列分类: ';
                foreach($couponInArrayFormat['categorynames'] as $sub){
                    $res .= ' "'.$sub.'"';
                }
                return $res;
                break;
            case 4:
                $res = '适用于下列商品222: ';
                foreach($couponInArrayFormat['goods_names'] as $sub){
                    $res .= ' "'.$sub.'"';
                }
                return $res;
                break;
            default:
                return ('Enjoy shopping');
        }
    }

    //在"优惠券中心"点击领取优惠券
    //需要提供$couponId
    public function getCoupon()
    {
        $couponId = \YunShop::request()->get('coupon_id');
        $memberId = \YunShop::request()->get('member_id');
        if(!$couponId){
            return $this->errorJson('没有提供优惠券ID','');
        }

        $coupon = Coupon::getCouponById($couponId)->first();

        if(!$coupon){
            return $this->errorJson('没有该优惠券,领取失败','');
        }

        $memberCoupon = new MemberCoupon;
        $count = MemberCoupon::getMemberCouponCount($memberId, $couponId);
        $couponMaxLimit = Coupon::getter($couponId, 'get_max'); //优惠券的限制每人的领取总数

        if($count >= $couponMaxLimit){
            return $this->errorJson('该用户已经达到个人领取上限','');
        }

        $data = [
            'uniacid' => \YunShop::app()->get('uniacid'),
            'uid' => \YunShop::app()->getMemberId(),
            'coupon_id' => $couponId,
            'get_type' => 1,
            'get_time' => strtotime('now'),
        ];
        $memberCoupon->fill($data);
        $validator = $memberCoupon->validator();
        if ($validator->fails()) {
            return $this->errorJson('领取失败', $validator->messages());
        } else {
            $res = $memberCoupon->save();
            if(!$res){
                return $this->errorJson('领取失败','');
            } else{ //按前端要求, 需要返回和 couponsForMember() 方法完全一致的数据 todo 单独提出一个方法, 复用
                $coupon = Coupon::getCouponById($couponId)
                                ->select([
                                    'id','name','coupon_method','deduct','discount','enough',
                                    'use_type', 'categorynames', 'goods_names', 'time_limit',
                                    'time_days', 'time_start', 'time_end', 'get_max', 'total', 'money', 'credit',
                                ])
                                ->withCount(['hasManyMemberCoupon'])
                                ->withCount(['hasManyMemberCoupon as member_got' => function($query) use($memberId){
                                    return $query->where('uid', '=', $memberId);
                                }])
                                ->first()
                                ->toArray();

                $usageLimit = array('api_limit' => self::usageLimitDescription($coupon)); //增加属性 - 优惠券的适用范围
                $coupon = array_merge($coupon, $usageLimit);

                //增加状态属性 todo 优化,合并成单独方法(因为和couponsForMember()方法用到的逻辑完全一致)
                $now = strtotime('now');
                if($coupon['time_limit'] == Coupon::ABSOLUTE_TIME_LIMIT && ($now > $coupon['time_end'])){ //优惠券已过期
                    $coupon['api_availability'] = self::NOT_AVAILABLE;
                    $coupon['api_status'] = self::OVERDUE;
                } elseif(($coupon['has_many_member_coupon_count'] >= $coupon['total']) && ($coupon['total'] != -1)){ //优惠券已抢光(PS.total=-1是bu限制数量)
                    $coupon['api_availability'] = self::NOT_AVAILABLE;
                    $coupon['api_status'] = self::EXHAUST;
                } elseif(($coupon['member_got_count'] >= $coupon['get_max']) && ($coupon['get_max'] != -1)){ //达到个人可领取的上限
                    $coupon['api_availability'] = self::NOT_AVAILABLE;
                    $coupon['api_status'] = self::ALREADY_GOT_AND_TOUCH_LIMIT;
                } elseif($coupon['member_got_count'] > 0){ //已领取,但没有达到个人可领取的上限
                    $coupon['api_availability'] = self::IS_AVAILABLE;
                    $coupon['api_status'] = self::ALREADY_GOT;
                } else{
                    $coupon['api_availability'] = self::IS_AVAILABLE;
                }
                return $this->successJson('ok', $coupon);
            }
        }
    }

}

