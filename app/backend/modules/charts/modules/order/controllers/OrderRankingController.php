<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/17 下午3:20
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\modules\order\models\Member;

class OrderRankingController extends ChartsController
{

    public function count()
    {


        return view('charts.order.order_ranking',$this->getResultData('order_count'))->render();
    }


    public function money()
    {
        return view('charts.order.order_ranking',$this->getResultData('order_money'))->render();
    }


    private function getResultData($param)
    {
        $data = $this->arrayKrSort($this->getData(),$param);
        return [
            'search'        => \YunShop::request()->search,
            'data'          => $this->getPageData($data),
            'pagination'    => $this->getPagination($data)
        ];
    }


    private function getData()
    {
        $data = $this->getMemberOrders();

        foreach ($data as $key => &$item) {

            //dd($item['order_count']);
            $item['order_count'] = 0;
            $item['order_money'] = 0;

            if ($item['has_many_order']) {
                $item['order_count'] = count($item['has_many_order']);
                $item['order_money'] = $this->orderMoneyTotal($item['has_many_order']);
            }
            unset($item['has_many_order']);
        }
        return $data;
    }


    /**
     * 会员 订单金额总和
     * @param array $array
     * @return int
     */
    private function orderMoneyTotal(array $array)
    {
        $price = 0;
        foreach ($array as $key => $item) {
            $price += $item['price'];
        }
        return $price;
    }


    private function getMemberOrders()
    {
        $search = \YunShop::request()->search;

        $query = Member::select('uid','nickname','realname','avatar','mobile')
            ->with(['hasManyOrder' => function($query) {
                $query->select('uid','price')->count();
            }]);


        if ($search['member_id']) {
            $query = $query->where('uid', $search['member_id']);
        }

        if ($search['member_info']) {
            $query = $query->where('nickname', 'like', '%' . $search['member_info'] . '%')
                ->orWhere('realname', 'like', '%' . $search['member_info'] . '%')
                ->orWhere('mobile', 'like', $search['member_info'] . '%');
        }

        $query = $query->get();
        return $query->isEmpty() ? [] : $query->toArray();
    }


}
