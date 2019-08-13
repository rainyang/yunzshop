<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/25
 * Time: 下午4:55
 */

namespace app\common\services\member\level;


use app\common\events\member\MemberLevelUpgradeEvent;
use app\common\events\member\MemberLevelValidityEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;
use app\common\models\notice\MessageTemp;
use app\common\models\Order;
use app\common\services\MessageService;

class LevelUpgradeService
{
    private $orderModel;

    private $memberModel;

    private $new_level;

    private $validity;

    public function checkUpgrade(AfterOrderReceivedEvent $event)
    {
        //event(new AfterOrderReceivedEvent(Order::where('status',3)->first()));
        $this->orderModel = $event->getOrderModel();
        $this->memberModel = MemberShopInfo::ofMemberId($this->orderModel->uid)->withLevel()->first();
        if (is_null($this->memberModel)) {
                \Log::info('---==会员不存在==----');
            return;
        }

        $result = $this->check(0);
                \Log::info('---==check方法结果==----', $result);


        $this->setValidity($result); // 设置会员等级期限
        if ($result) {
            return $this->upgrade($result);
        }
        return '';
    }

    public function checkUpgradeAfterPaid(AfterOrderPaidEvent $event)
    {
        $set = Setting::get('shop.member');

        $this->orderModel = $event->getOrderModel();

        if (!is_null($set)) {
            $uniacid = $this->orderModel->uniacid;
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;

            $set = Setting::get('shop.member');
        }

        if ($set['level_after'] != 1) {
                \Log::debug('后台未开启支付升级设置');
            return;
        }

        $this->memberModel = MemberShopInfo::ofMemberId($this->orderModel->uid)->withLevel()->first();

        if (is_null($this->memberModel)) {
                \Log::debug('暂无该会员信息');
            return;
        }

        $result = $this->check(1);
        $this->setValidity($result); // 设置会员等级期限
        \Log::debug('打印结果',$result);
        if ($result) {
            return $this->upgrade($result);
        }

        return '';
    }

    public function setValidity($isUpgrate = false)
    {
        $set = Setting::get('shop.member');
        \Log::debug('打印set',$set);
        if (!$set['term']) {
            return;
        }
        \Log::debug('打印validity',$this->validity);
        if (!$this->validity['is_goods']) {
            return;
        }
        if ($this->validity['upgrade']) {
            $validity = $this->new_level->validity * $this->validity['goods_total'];
        } else {
            //bug 会员当前等级 > 新的等级  有效期不应该叠加, 当等级相等时才叠加
            //$validity = $this->memberModel->validity + $this->new_level->validity * $this->validity['goods_total'];
            
            if ($this->validity['superposition']) {
                $validity = $this->memberModel->validity + $this->new_level->validity * $this->validity['goods_total'];
            }
        }

        \Log::debug('打印会员当前等级',[$validity,$isUpgrate]);

        if (isset($validity)) {
            $this->memberModel->validity = $validity;
            $this->memberModel->downgrade_at = 0;
            $this->memberModel->save();

            if (!$isUpgrate) {
                
                $levelId = intval($this->new_level->id);
                event(new MemberLevelValidityEvent($this->memberModel, $this->validity['goods_total'], $levelId));
            }
        }

    }

    private function check($status)
    {
        $set = Setting::get('shop.member');
                \Log::info('---==等级设置信息==----', [unserialize($set), json_decode($set, true)]);
        \Log::debug('打印level_type',[$set['level_type'],$set]);
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
                    if ($set['level_after']) {
                        $this->new_level = $this->checkGoodsId();
                    }
                } else {
                    if(!$set['level_after']) {
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
                
                \Log::info('---==会员等级信息==----', [$memberLevel, $this->new_level->level]);

            if ($this->new_level->level == $memberLevel) {
                $this->validity['superposition'] = true; //会员期限叠加
            }

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

        //获取满足条件的最高等级
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
        
        \Log::debug('---==get_order_model==---', $this->orderModel);
        \Log::debug('---==get_member_model==---', $this->memberModel);
        // $level = MemberLevel::uniacid()->select('id', 'level', 'level_name', 'goods_id', 'validity')->whereIn('goods_id', $goodsIds)->orderBy('level', 'desc')->first();  // 原先逻辑为购买指定某一商品即可升级, 现为购买指定任易商品即可升级
        //获取
        
        $levelid = MemberLevel::find($this->memberModel->level_id);
        
        \Log::debug('---==levelid==---', $levelid);

        $levels = MemberLevel::uniacid()->where('level', '>', $levelid->level ? : 0)->select('id', 'level', 'level_name', 'goods_id', 'validity')->orderBy('level', 'desc')->get();

        \Log::debug('---==levels==---', $levels);

        $this->validity['is_goods'] = true; // 商品升级 开启等级期限

        foreach ($this->orderModel->hasManyOrderGoods as $time) {
            // if ($time->goods_id == $level->goods_id) { // 原先逻辑为购买指定某一商品即可升级, 现为购买指定任易商品即可升级
            
            foreach ($levels as  $level) {
                
                $levelGoodsId = explode(',', $level->goods_id);
        
        \Log::debug('---==levelGoodsId==---', $levelGoodsId);
        \Log::debug('---==checkInarray==---', in_array($time->goods_id, $levelGoodsId));

                if (in_array($time->goods_id, $levelGoodsId)) {
                    
                    $this->validity['goods_total'] = $time->total;

                    $reallevel = MemberLevel::find($level->id);

                    \Log::debug('---===member_level_upgrade===---', $time->total);

                    //开启一卡通
                    if (app('plugins')->isEnabled('universal-card')) {
                        
                        if ($time->goods_option_id) {
                            $level->validity = (new \Yunshop\UniversalCard\services\LevelUpgradeService())->upgrade($level->id, $time->goods_option_id);
                        }
                    }
                }
            }
        }
        // return $level ?: [];
        return $reallevel ?: [];
    }

    private function upgrade($levelId)
    {

        $this->memberModel->level_id = $levelId;
        $this->memberModel->upgrade_at = time();
        \Log::debug('$this->memberModel->level_id',$this->memberModel->level_id);

        if ($this->memberModel->save()) {
            //会员等级升级触发事件
            event(new MemberLevelUpgradeEvent($this->memberModel,false));

            event(new MemberLevelValidityEvent($this->memberModel, $this->validity['goods_total'], $levelId));
            \Log::debug('会员等级升级触发事件');
            $this->notice();
            \Log::info('会员ID' . $this->memberModel->member_id . '会员等级升级成功，等级ID' . $levelId);
        } else {
            \Log::debug('失败');
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
            ['name' => '有效期', 'value' => $this->memberModel->validity.'天'],
        ];

        $msg = MessageTemp::getSendMsg($template_id, $params);
        if (!$msg) {
            return;
        }
        $news_link = MessageTemp::find($template_id)->news_link;
        $news_link = $news_link ?:'';
        MessageService::notice(MessageTemp::$template_id, $msg, $memberModel->uid,'',$news_link);
    }


}
