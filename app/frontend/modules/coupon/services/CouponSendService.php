<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/25 下午2:15
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\coupon\services;


use app\common\models\CouponLog;
use app\common\models\MemberCoupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponSendService
{
    protected $memberId;

    protected $couponId;

    protected $get_type;

    protected $send_total;

    protected $relation;  //关联，订单号 或 ID值

    protected $transferId; //转让者ID



    //todo 确定 get_type 类型，增加类型数据验证，



    public function sendCouponsToMember($memberId, array $couponIds, $get_type = '0', $relation = '', $transferId = '')
    {
        if (empty($memberId) || !is_numeric($memberId)) {
            Log::info('优惠劵发送接口调用失败，会员ID错误！', print_r($memberId,true));
            return null;
        }
        if (empty($couponIds) || !is_array($couponIds)) {
            Log::info('优惠劵发送接口调用失败，优惠劵ID错误！', print_r($couponIds,true));
            return null;
        }


        $this->memberId = $memberId;
        $this->get_type = $get_type;
        $this->send_total = count($couponIds);
        $this->relation = $relation;
        $this->transferId = $transferId;


        $data = array();
        $log_data = array();
        foreach ($couponIds as $couponId) {
            $this->couponId = $couponId;

            $data[] = $this->getMemberCouponData();
            $log_data[] = $this->getCouponLogData();
        }

//dump($log_data);
//dd($data);
        return $this->updateMemberCoupons($data, $log_data);
    }


    public function sendCouponToMembers(array $memberIds, $couponId, $get_type = '0', $relation = '',$transferId)
    {
        if (empty($couponId) || !is_numeric($couponId)) {
            Log::info('优惠劵发送接口调用失败，会员ID错误！', print_r($couponId,true));
            return null;
        }
        if (empty($memberIds) || !is_array($memberIds)) {
            Log::info('优惠劵发送接口调用失败，优惠劵ID错误！', print_r($memberIds,true));
            return null;
        }

        $this->couponId = $couponId;
        $this->get_type = $get_type;
        $this->send_total = 1;
        $this->relation = $relation;
        $this->transferId = $transferId;



        $data = array();
        $log_data = array();
        foreach ($memberIds as $memberId) {
            $this->memberId = $memberId;

            $data[] = $this->getMemberCouponData();
            $log_data[] = $this->getCouponLogData();
        }

        return $this->updateMemberCoupons($data, $log_data);
    }


    protected function updateMemberCoupons(array $data, array $log_data)
    {
        if (empty($data) || !is_array($data)) {
            Log::info('优惠劵发送接口调用失败，修改会员优惠劵数据有误！', print_r($data,true));
            return null;
        }
        if (empty($log_data) || !is_array($log_data)) {
            Log::info('优惠劵发送接口调用失败，优惠劵变动记录数据有误！', print_r($log_data,true));
            return null;
        }

        DB::transaction(function ()use($data,$log_data) {
            CouponLog::insert($log_data);
            MemberCoupon::insert($data);
        });

        return true;
    }


    private function getCouponLogData()
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'logno'         => $this->getCouponLogRemark(),
            'member_id'     => $this->memberId,
            'couponid'      => $this->couponId,
            'paystatus'     => 0, //todo 手动发放的不需要支付?
            'creditstatus'  => 0, //todo 手动发放的不需要支付?
            'paytype'       => 0, //todo 这个字段什么含义?
            'getfrom'       => $this->get_type,
            'status'        => 0,
            'createtime'    => time(),
        ];
    }


    private function getMemberCouponData()
    {
        return [
            'uniacid'   => \YunShop::app()->uniacid,
            'uid'       => $this->memberId,
            'coupon_id' => $this->couponId,
            'get_type'  => $this->get_type,
            'used'      => 0,
            'get_time'  => time(),
        ];
    }


    //todo get_type 使用常量判断，目前不确定常量放在哪里

    private function getCouponLogRemark()
    {
        $adminId = \YunShop::app()->uid;

        switch ($this->get_type) {
            //case '0':
                //$remark = '手动发放优惠券: 管理员【ID:' . $adminId . '】成功发放 ' . $this->send_total . ' 张优惠券【优惠劵ID:' . $this->couponId . '】给用户【会员ID:' . $this->memberId . '】';
                //break;
            //case '1':
                //$remark = '会员领取优惠券: 会员【ID:' . $this->memberId . '】成功领取' . $this->send_total . ' 张优惠券【优惠劵ID:' . $this->couponId . '】';
                //break;
            //case '3':
                //$remark = '';
                //break;
            case '4':
                $remark = '购物赠送优惠券: 订单:'.$this->relation.'完成，成功赠送会员【ID:' . $this->memberId . '】1张优惠券【优惠劵ID:' . $this->couponId . '】';
                break;
            case '5':
                $remark = '会员转赠: 会员【ID:' . $this->memberId . '】优惠劵变动 1张【优惠劵ID:' . $this->couponId . '】转让会员【ID:'.$this->transferId.'】';
                break;
            case '6':
                $remark = '签到奖励: 会员【ID:' . $this->memberId . '】优惠劵变动 1张【优惠劵ID:' . $this->couponId . '】';
                break;
            default:
                $remark = '未知优惠劵变动：会员【ID:' . $this->memberId . '】优惠劵变动 1张【优惠劵ID:' . $this->couponId . '】';
                break;
        }
        return $remark;
    }




}
