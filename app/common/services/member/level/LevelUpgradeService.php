<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/25
 * Time: 下午4:55
 */

namespace app\common\services\member\level;


use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;
use app\common\models\notice\MessageTemp;
use app\common\models\Order;
use app\common\services\MessageService;
use Monolog\Handler\IFTTTHandler;

class LevelUpgradeService
{
    private $orderModel;

    private $memberModel;

    private $new_level;

    private $validity;

    public function checkUpgrade(AfterOrderReceivedEvent $event)
    {
        $this->orderModel = $event->getOrderModel();
        $this->memberModel = MemberShopInfo::ofMemberId($this->orderModel->uid)->withLevel()->first();

        if (is_null($this->memberModel)) {
            return;
        }

        $result = $this->check();

        $this->setValidity(); // 设置会员等级期限

        if ($result) {
            return $this->upgrade($result);
        }
        return '';
    }

    public function checkUpgradeAfterPaid(AfterOrderPaidEvent $event)
    {
        $this->orderModel = $event->getOrderModel();
        $this->memberModel = MemberShopInfo::ofMemberId($this->orderModel->uid)->withLevel()->first();

        if (is_null($this->memberModel)) {
            return;
        }

        $result = $this->check(1);

        $this->setValidity(); // 设置会员等级期限

        if ($result) {
            return $this->upgrade($result);
        }
        return '';
    }

    public function setValidity()
    {
        $set = Setting::get('shop.member');
        if (!$set['term']) {
            return;
        }

        if (!$this->validity['is_goods']) {
            return;
        }

        if ($this->validity['upgrade']) {
            $validity = $this->new_level->validity * $this->validity['goods_total'];
        } else {
            $validity = $this->memberModel->validity + $this->new_level->validity * $this->validity['goods_total'];
        }

        $this->memberModel->validity = $validity;

        $this->memberModel->save();
    }


    private function check($status)
    {
        $set = Setting::get('shop.member');

        //获取可升级的最高等级
        switch ($set['level_type']) {
            case 0:
                $this->new_level = $this->checkOrderMoney();
                break;
            case 1:
                $this->new_level = $this->checkOrderCount();
                break;
            case 2:
                if ($status == 1) {
                    if ($set['level_after'] == 1) {
                        $this->new_level = $this->checkGoodsId();
                    }
                } else {
                    if(empty($set['level_after'])) {
                        $this->new_level = $this->checkGoodsId();
                    }
                }
                break;
            default:
                $level = '';
        }

        //比对当前等级权重，判断是否升级
        if ($this->new_level) {
            $memberLevel = isset($this->memberModel->level->level) ? $this->memberModel->level->level : 0;

            if ($this->new_level->level > $memberLevel) {
                $this->validity['upgrade'] = true; // 会员期限 升级 期限叠加
                return $this->new_level->id;
            }
            return '';
        }
        return '';
    }

    /**
     * 会员完成订单总金额，返回对应最高会员等级数组
     * @return mixed
     */
    private function checkOrderMoney()
    {
        $set = Setting::get('shop.member');
        if ($set['level_after'] == 1) {
            //付款后
            $orderMoney = Order::where('uid', $this->orderModel->uid)->whereBetween('status', [Order::WAIT_SEND,Order::COMPLETE])->sum('price');
        } else {
            //完成后
            $orderMoney = Order::where('uid', $this->orderModel->uid)->where('status', Order::COMPLETE)->sum('price');
        }

        $level = MemberLevel::uniacid()->select('id', 'level', 'level_name')->whereBetween('order_money', [1, $orderMoney])->orderBy('level', 'desc')->first();

        return $level;
    }

    /**
     * 会员完成订单总数量，返回对应最高会员等级数组
     * @return mixed
     */
    private function checkOrderCount()
    {
        $set = Setting::get('shop.member');
        if ($set['level_after'] == 1) {
            //付款后
            $orderCount = Order::where('uid', $this->orderModel->uid)->whereBetween('status', [Order::WAIT_SEND,Order::COMPLETE])->count();
        } else {
            //完成后
            $orderCount = Order::where('uid', $this->orderModel->uid)->where('status', Order::COMPLETE)->count();
        }

        $level = MemberLevel::uniacid()->select('id', 'level', 'level_name')->whereBetween('order_count', [1, $orderCount])->orderBy('level', 'desc')->first();

        return $level;
    }

    /**
     * 当前订单中满足升级会员等级的 最高会员等级数组，空返回 array
     * @return array
     */
    private function checkGoodsId()
    {
        $goodsIds = array_pluck($this->orderModel->hasManyOrderGoods->toArray(), 'goods_id');

        $level = MemberLevel::uniacid()->select('id', 'level', 'level_name', 'goods_id', 'validity')->whereIn('goods_id', $goodsIds)->orderBy('level', 'desc')->first();
        $this->validity['is_goods'] = true; // 商品升级 开启等级期限

        foreach ($this->orderModel->hasManyOrderGoods as $time) {
            if ($time->goods_id == $level->goods_id) {
                $this->validity['goods_total'] = $time->total;
            }
        }


        return $level ?: [];
    }


    private function upgrade($levelId)
    {
        $this->memberModel->level_id = $levelId;

        if ($this->memberModel->save()) {
            $this->notice();
            \Log::info('会员ID' . $this->memberModel->member_id . '会员等级升级成功，等级ID' . $levelId);
        } else {
            \Log::info('会员ID' . $this->memberModel->member_id . '会员等级升级失败，等级ID' . $levelId);
        }

        //todo 会员等级升级通知
        return true;
    }

    private function notice()
    {
        $template_id = \Setting::get('shop.notice.customer_upgrade');

        if (!trim($template_id)) {
            return '';
        }
        $memberModel = Member::select('uid', 'nickname', 'realname')->where('uid', $this->memberModel->member_id)->with('hasOneFans')->first();

        $member_name = $memberModel->realname ?: $memberModel->nickname;

        $set = \Setting::get('shop.member');
        $old_level = $set['level_name'] ?: '普通会员';
        $old_level = $this->memberModel->level->level_name ?: $old_level;

        $params = [
            ['name' => '粉丝昵称', 'value' => $member_name],
            ['name' => '旧等级', 'value' => $old_level],
            ['name' => '新等级', 'value' => $this->new_level->level_name],
            ['name' => '时间', 'value' => date('Y-m-d H:i',time())],
        ];

        $msg = MessageTemp::getSendMsg($template_id, $params);
        if (!$msg) {
            return;
        }

        MessageService::notice(MessageTemp::$template_id, $msg, $memberModel->uid);
    }


}
