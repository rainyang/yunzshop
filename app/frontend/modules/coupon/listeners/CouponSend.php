<?php
namespace app\frontend\modules\coupon\listeners;

use app\common\facades\Setting;
use app\common\models\Coupon;
use app\common\models\GoodsCouponQueue;
use app\common\models\UniAccount;
use app\Jobs\addSendCouponJob;
use app\Jobs\addSendCouponLogJob;
use app\Jobs\updateCouponQueueJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\backend\modules\coupon\services\MessageNotice;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/12
 * Time: 下午4:28
 */
class CouponSend
{
    use DispatchesJobs;
    public $set;
    public $setLog;
    public $uniacid;

    public function handle()
    {
        \Log::info('发放优惠券处理');
        set_time_limit(0);
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->uniacid = $u->uniacid;
            $this->setLog = Setting::get('shop.coupon_send_log');
            $this->sendCoupon();
        }
    }

    public function sendCoupon()
    {
        if (date('H') != '0') {
            return;
        }
        if (date('d') != '1') {
            return;
        }
        if (date('m') == $this->setLog['current_m']) {
            return;
        }
        $this->setLog['current_m'] = date('m');
        Setting::set('shop.coupon_send_log', $this->setLog);

        $couponSendQueues = GoodsCouponQueue::getCouponQueue()->get();
        $surplusNums = [];//用于统计 剩余未发放数量
        foreach ($couponSendQueues as $couponSendQueue) {
            $updatedData = [];
            $coupon = $couponSendQueue->hasOneCoupon;
            $surplusNums['coupon_id_' . $coupon->id] = isset($surplusNums['coupon_id_' . $coupon->id])
                ? $surplusNums['coupon_id_' . $coupon->id]
                : $coupon->surplus;

            if ($surplusNums['coupon_id_' . $coupon->id] <= 0) {
                continue;
            }
            $this->sendCouponForMember($couponSendQueue);//发放优惠券到会员
            //发送获取通知
            MessageNotice::couponNotice($couponSendQueue->coupon_id,$couponSendQueue->uid);
            $this->sendCouponLog($couponSendQueue);//发放优惠券LOG

            $condition = [
                'id' => $couponSendQueue->id
            ];
            $updatedData['end_send_num'] = $couponSendQueue->end_send_num + 1;
            if ($updatedData['end_send_num'] == $couponSendQueue->send_num) {
                $updatedData['status'] = 1;
            }
            $this->dispatch((new updateCouponQueueJob($condition, $updatedData)));

            $surplusNums['coupon_id_' . $coupon->id] -= 1;
        }
    }

    public function sendCouponForMember($couponSendQueue)
    {
        $data = [
            'uniacid' => $couponSendQueue->uniacid,
            'uid' => $couponSendQueue->uid,
            'coupon_id' => $couponSendQueue->coupon_id,
            'get_type' => 0,
            'used' => 0,
            'get_time' => strtotime('now'),
        ];
        $this->dispatch((new addSendCouponJob($data)));

    }

    public function sendCouponLog($couponSendQueue)
    {
        $log = '购买商品发放优惠券成功: 商品( ID 为 ' . $couponSendQueue->goods_id . ' )成功发放 1 张优惠券( ID为 ' . $couponSendQueue->coupon_id . ' )给用户( Member ID 为 ' . $couponSendQueue->uid . ' )';
        $logData = [
            'uniacid' => $couponSendQueue->uniacid,
            'logno' => $log,
            'member_id' => $couponSendQueue->uid,
            'couponid' => $couponSendQueue->coupon_id,
            'paystatus' => 0, //todo 购买商品发放的不需要支付?
            'creditstatus' => 0, //todo 购买商品发放的不需要支付?
            'paytype' => 0, //todo 这个字段什么含义?
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        $this->dispatch((new addSendCouponLogJob($logData)));

    }


    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Coupon-send', '*/10 * * * * *', function () {
                $this->handle();
                return;
            });
        });
    }
}