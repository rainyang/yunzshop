<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\backend\modules\charts\modules\member\models\Member;
use app\backend\modules\charts\modules\member\models\YzMember;
use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class RelationController extends BaseController
{

    private $page_size = 10;


    public function index()
    {

        $search = \YunShop::request()->search;
        $member_ids = $this->getMemberIds($search);
        $member_ids = empty($member_ids) ? [] : $member_ids;


        $data = $this->getData($member_ids);

        //分页
        $page_index = \YunShop::request()->page ?: 0;
        $page = PaginationHelper::show(sizeof($data) - $this->page_size, $page_index, $this->page_size);

        $start = $page_index * $this->page_size;
        $start = sizeof($data) >= $start ? $start : 0;
        $end = $start + $this->page_size;

        $data = array_where($data, function ($value, $key) use($start,$end) {
            return $key >= $start && $key < $end;
        });

        return view('charts.member.relation',['data' => $data, 'page' => $page, 'search' => \YunShop::request()->search])->render();
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

        //递归排序
        $data = array_values(array_sort($data, function ($value) {
            return $value['order_money_total'];
        }));
        krsort($data);
        return  array_values($data);
    }


    private function getLv1CompleteOrderMoney($member_id)
    {
        $member_ids = YzMember::getMemberOffline($member_id,1);
        return $this->getMemberCompleteOrderMoney($member_ids);
    }

    private function getLv2CompleteOrderMoney($member_id)
    {
        $member_ids = YzMember::getMemberOffline($member_id,2);
        return $this->getMemberCompleteOrderMoney($member_ids);
    }

    private function getLv3CompleteOrderMoney($member_id)
    {
        $member_ids = YzMember::getMemberOffline($member_id,3);
        return $this->getMemberCompleteOrderMoney($member_ids);
    }

    private function getMemberCompleteOrderMoney($member_ids)
    {
        return Order::whereIn('uid',$member_ids)->where('status',Order::COMPLETE)->sum('price');
    }

    private function getMemberIds($search)
    {
        $query = Member::select('uid','nickname','realname','avatar')
            ->with(['yzMember' => function($query) {
                $query->select('member_id','parent_id','relation');
            }]);


        if ($search['member_id']) {
            $query = $query->where('uid',$search['member_id']);
        }
        if ($search['member_info']) {
            $query = $query->where('nickname', 'like', '%' . $search['member_info'] . '%')
                ->orWhere('realname', 'like', '%' . $search['member_info'] . '%')
                ->orWhere('mobile', 'like', $search['member_info'] . '%');
        }
        return $query->get();
    }




}
