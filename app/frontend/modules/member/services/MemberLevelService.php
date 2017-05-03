<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/5/2
 * Time: 下午2:28
 */

namespace app\frontend\modules\member\services;


use app\backend\modules\member\models\MemberShopInfo;
use app\backend\modules\order\models\Order;
use app\common\facades\Setting;
use app\frontend\modules\goods\models\goods\MemberLevel;

class MemberLevelService
{
    private $orderModel;

    private $memberModel;



    public function test($model)
    {
        //dd($model);
        $this->orderModel = $model;

        $level = $this->getUpgradeLevel();
        if ($level) {
            $level = $level->toArray();
            $member_level = $this->getMemberLevel();
            $member_level = $member_level ? $member_level->toArray() : array();
            if ($level['level'] > $member_level['level']['level'] ?: 0) {

                //dd($level);
                return $this->updateMemberLevel($level['id']);
                //dd('up');
            }
        }
        return;
    }

    private function updateMemberLevel($levelId)
    {

        $this->memberModel->level_id = $levelId;
        //dd($this->memberModel);
        if ($this->memberModel->save()) {
            return true;
        }
        return '会员等级升级失败';
    }

    private function isUpgrade()
    {


        return false;
    }

    private function orderMoneyUpgrade()
    {
        $orderMoney = Order::where('uid', $this->orderModel->uid)->uniacid()->sum('price');

        $level = MemberLevel::uniacid()
            ->select('id','level','level_name')
            ->whereBetween('order_money', [1,$orderMoney] )
            ->orderBy('level', 'desc')
            ->first();

        return $level;
    }

    private function orderCountUpgrade()
    {
        $orderCount = Order::where('uid', $this->orderModel->uid)->uniacid()->count();

        $level = MemberLevel::uniacid()
            ->select('id','level','level_name')
            ->whereBetween('order_count', [1,$orderCount] )
            ->orderBy('level', 'desc')
            ->first();

        return $level;
    }

    private function designatedGoodsUpgrade()
    {
        $ids = $this->getOrderGoodsIds();

        $level = MemberLevel::uniacid()
            ->select('id','level','level_name')
            ->whereIn('goods_id', $ids)
            ->first();


        return $level;
    }

    //会员当前等级
    private function getMemberLevel()
    {
        return $this->memberModel = MemberShopInfo::getMemberLevel($this->orderModel->uid);
    }



    private function getUpgradeLevel()
    {
        $set = Setting::get('shop.member');

        switch ($set['level_type'])
        {
            case 0:
                return $this->orderMoneyUpgrade();
                break;
            case 1:
                return $this->orderCountUpgrade();
                break;
            case 2:
                return $this->designatedGoodsUpgrade();
                break;
            default:
                return true;
        }
    }

    private function getOrderGoodsIds()
    {
        $goods = $this->orderModel->hasManyOrderGoods;
        $ids = [];
        if ($goods) {
            $goods = $goods->toArray();
            foreach ($goods as $key => $value) {
                //echo '<pre>'; print_r($value); exit;
                $ids[] = $value['goods_id'];
            }
        }
        return $ids;
    }


}