<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/25
 * Time: 下午4:55
 */

namespace app\common\services\member\level;


use app\common\events\order\AfterOrderReceivedEvent;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;
use app\common\models\Order;
use app\common\services\MessageService;
use Monolog\Handler\IFTTTHandler;

class LevelUpgradeService
{
    private $orderModel;

    private $memberModel;

    private $new_level;

    public function checkUpgrade(AfterOrderReceivedEvent $event)
    {
        $this->orderModel   = $event->getOrderModel();
        $this->memberModel  = MemberShopInfo::ofMemberId($this->orderModel->uid)->withLevel()->first();


        $result = $this->check();
        if ($result) {
            return $this->upgrade($result);
        }
        return '';
    }


    private function check()
    {
        $set = Setting::get('shop.member');

        //获取可升级的最高等级
        switch ($set['level_type'])
        {
            case 0:
                $this->new_level =  $this->checkOrderMoney();
                break;
            case 1:
                $this->new_level =  $this->checkOrderCount();
                break;
            case 2:
                $this->new_level =  $this->checkGoodsId();
                break;
            default:
                $level =  '';
        }

        //比对当前等级权重，判断是否升级
        if ($this->new_level) {
            $memberLevel = isset($this->memberModel->level->level) ? $this->memberModel->level->level : 0;

            if ($this->new_level->level > $memberLevel) {
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
        $orderMoney = Order::where('uid', $this->orderModel->uid)->where('status', Order::COMPLETE)->sum('price');

        $level = MemberLevel::uniacid()->select('id','level','level_name')->whereBetween('order_money', [1,$orderMoney] )->orderBy('level', 'desc')->first();

        return $level;
    }

    /**
     * 会员完成订单总数量，返回对应最高会员等级数组
     * @return mixed
     */
    private function checkOrderCount()
    {
        $orderCount = Order::where('uid', $this->orderModel->uid)->count();

        $level = MemberLevel::uniacid()->select('id','level','level_name')->whereBetween('order_count', [1,$orderCount] )->orderBy('level', 'desc')->first();

        return $level;
    }

    /**
     * 当前订单中满足升级会员等级的 最高会员等级数组，空返回 array
     * @return array
     */
    private function checkGoodsId()
    {
        $goodsIds = array_pluck($this->orderModel->hasManyOrderGoods->toArray(), 'goods_id');

        $level = MemberLevel::uniacid()->select('id','level','level_name')->whereIn('goods_id', $goodsIds)->orderBy('level', 'desc')->first();

        return $level ?: [];
    }


    private function upgrade($levelId)
    {
        $this->memberModel->level_id = $levelId;

        if ($this->memberModel->save()) {
            $this->notice();
            \Log::info('会员ID'.$this->memberModel->member_id . '会员等级升级成功，等级ID' . $levelId);
        } else {
            \Log::info('会员ID'.$this->memberModel->member_id . '会员等级升级失败，等级ID' . $levelId);
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
        $memberModel = Member::select('uid','nickname','realname')->where('uid',$this->memberModel->member_id)->with('hasOneFans')->first();

        $member_name = $memberModel->realname ?: $memberModel->nickname;
        $msg              = array(
            'first' => array(
                'value' => "亲爱的" . $member_name . ', 恭喜您成功升级！',
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'title' => '任务名称',
                'value' => '会员升级',
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'title' => '通知类型',
                'value' => '您会员等级从 ' . $this->memberModel->level->level_name . ' 升级为 ' . $this->new_level->level_name . ', 特此通知!',
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => "\r\n您即可享有" . $this->new_level->level_name . '的专属优惠及服务！',
                "color" => "#4a5077"
            )
        );

        if (isset($memberModel->hasOneFans) && !empty($memberModel->hasOneFans->openid) && $memberModel->hasOneFans->follow) {
            MessageService::notice($template_id,$msg,$memberModel->uid);
        }
    }



}
