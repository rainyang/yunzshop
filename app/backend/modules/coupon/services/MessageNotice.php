<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/13
 * Time: 11:59
 */
namespace app\backend\modules\coupon\services;

use app\common\services\MessageService;
use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use app\common\models\Coupon;
use app\backend\modules\member\models\Member;

class MessageNotice extends MessageService
{
    public static function couponNotice($couponDate,$memberId)
    {
        $couponNotice = Setting::get('coupon.coupon_notice');
        $member = Member::getMemberInfoById($memberId);
//        dump(Coupon::getPromotionMethod($couponDate->id));exit();
        $temp_id = $couponNotice;
        if (!$temp_id) {
            return false;
        }
        static::messageNotice($temp_id,$couponDate, $member);
        return true;
    }
    public static function messageNotice($temp_id, $couponId, $member, $uniacid = '')
    {
        $couponDate = Coupon::getCouponById($couponId);
        $coupon_scope = implode('',Coupon::getApplicableScope($couponDate->id));

        $coupon_mode = Coupon::getPromotionMethod($couponDate->id);
        if($coupon_mode['type'] == 1) {
            $coupon_mode['content'] = "立减".$coupon_mode['mode']."元";
        } elseif ($coupon_mode['type'] == 2) {
            $coupon_mode['content'] = "折扣".$coupon_mode['mode']."元";
        }
        $params = [
            ['name' => '昵称', 'value' => $member['nickname']],
            ['name' => '优惠券名称', 'value' => $couponDate->name],
            ['name' => '优惠券使用范围', 'value' => $coupon_scope],
            ['name' => '优惠券使用条件', 'value' => $couponDate->enough],
            ['name' => '优惠方式', 'value' => $coupon_mode['content']],
            ['name' => '过期时间', 'value' => $couponDate->time_end],
            ['name' => '获得时间', 'value' => date('Y-m-d H:i:s', time())],
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return false;
        }
        MessageService::notice(MessageTemp::$template_id, $msg, $member->uid, $uniacid);
    }
}