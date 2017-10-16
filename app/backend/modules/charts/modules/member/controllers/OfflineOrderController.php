<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\backend\modules\order\models\Order;
use app\common\helpers\PaginationHelper;

class OfflineOrderController extends OfflineCountController
{
    public function index()
    {
        $data = $this->getData($this->getAllMembers());

        //分页
        $page_index = \YunShop::request()->page ?: 1;
        $page = PaginationHelper::show(sizeof($data) - $this->page_size, $page_index, $this->page_size);

        $start = $page_index * $this->page_size - $this->page_size;
        $end = $start + $this->page_size;

        $data = array_where($data, function ($value, $key) use($start,$end) {
            return $key >= $start && $key < $end;
        });

        return view('charts.member.offline_order',['data' => $data, 'page' => $page, 'search' => \YunShop::request()->search])->render();
    }


    private function getData(array $member_ids)
    {
        $data = [];
        foreach ($member_ids as $key => $member) {

            $lv1 = $this->getLv1CompleteOrderMoney($member->uid);
            $lv2 = $this->getLv2CompleteOrderMoney($member->uid);
            $lv3 = $this->getLv3CompleteOrderMoney($member->uid);

            $data[] = [
                'member_id'         => $member->uid,
                'member_name'       => $member->realname ?: $member->nickname,
                'avatar'            => $member->avatar,
                'lv1_order_money'   => $lv1,
                'lv2_order_money'   => $lv2,
                'lv3_order_money'   => $lv3,
                'order_money_total' => $lv1 + $lv2 + $lv3

            ];
        }

       return $this->arraySort($data, 'order_money_total');
    }


    private function getLv1CompleteOrderMoney($member_id)
    {
        return $this->getMemberCompleteOrderMoney($this->getLv1Offline($member_id));
    }

    private function getLv2CompleteOrderMoney($member_id)
    {
        return $this->getMemberCompleteOrderMoney($this->getLv2Offline($member_id));
    }

    private function getLv3CompleteOrderMoney($member_id)
    {
        return $this->getMemberCompleteOrderMoney($this->getLv3Offline($member_id));
    }

    private function getMemberCompleteOrderMoney($member_ids)
    {
        return Order::whereIn('uid',$member_ids)->where('status',Order::COMPLETE)->sum('price');
    }




}
