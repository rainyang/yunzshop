<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 下午9:44
 */

namespace app\frontend\modules\finance\services;

use app\backend\modules\member\models\Member;
use Setting;

class CalculationPointService
{
    private $order_goods_model;
    private $point_set;
    private $point;
    private $member;

    public function __construct($order_goods_model, $member_id)
    {
        $this->verifyPointSet();
        $this->vetifyMemberPoint($member_id);
        $this->order_goods_model = $order_goods_model;
        $this->calculationPoint();
        return [
            'point_money' => $this->point * $this->point_set['money']
        ];
    }

    private function verifyPointSet()
    {
        if (Setting::get('point.set')['point_deduct'] == 0) {
            return false;
        }
        $this->point_set = Setting::get('point.set');
    }

    private function vetifyMemberPoint($member_id)
    {
        if (Member::deleteMemberInfoById($member_id)['credit1'] <= 0) {
            return false;
        }
        $this->member = Member::deleteMemberInfoById($member_id);
    }

    private function calculationPoint()
    {
        foreach ($this->order_goods_model as $goods_model) {
            $this->calculationMemberPoint($this->getGoodsPoint($goods_model));
        }
    }

    private function getGoodsPoint($goods_model)
    {
        if ($goods_model->hasOneSale->max_point_deduct > 0) {
            $goods_point = $goods_model->hasOneSale->max_point_deduct / $this->point_set['money'];
            return $goods_point;
        } else if ($this->point_set['money_max'] > 0) {
            $goods_point = $this->point_set['money_max'] / $this->point_set['money'];
            return $goods_point;
        }
    }

    private function calculationMemberPoint($goods_point)
    {
        if ($this->member['credit1'] == 0) {
            return;
        }
        if ($goods_point >= $this->member['credit1']) {
            $this->point += $this->member['credit1'];
            $this->member['credit1'] = 0;
        } else {
            $this->point += $goods_point;
            $this->member['credit1'] = $this->member['credit1'] - $goods_point;
        }
    }
}