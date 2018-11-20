<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 11:32
 */

namespace app\backend\modules\charts\modules\member\services;


use app\backend\modules\charts\modules\member\models\MemberLowerOrder;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;

class LowerOrderService
{
    public function memberOrder()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $uniacid = \YunShop::app()->uniacid;
            $member_1 = DB::table('yz_member_children')->select('member_id', DB::raw('group_concat(child_id) as child'))->where('level', 1)->groupBy('member_id')->get()->toArray();

            foreach ($member_1 as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['first_order_quantity'] = DB::table('yz_order')->select(DB::raw('count(id) as total'))->where('uid', $item['child'])->get()->toArray()[0]['total'] ?: 0;
                $result[$item['member_id']]['first_order_amount'] = intval(DB::table('yz_order')->select(DB::raw('sum(price) as money'))->where('uid', $item['child'])->get()->toArray()[0]['money']) ?: 0;
                $result[$item['member_id']]['second_order_quantity'] = 0;
                $result[$item['member_id']]['second_order_amount'] = 0;
                $result[$item['member_id']]['third_order_quantity'] = 0;
                $result[$item['member_id']]['third_order_amount'] = 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount'];
            }

            $member_2 = DB::table('yz_member_children')->select('member_id', DB::raw('group_concat(child_id) as child'))->where('level', 2)->groupBy('member_id')->get()->toArray();
            foreach ($member_2 as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['second_order_quantity'] = DB::table('yz_order')->select(DB::raw('count(id) as total'))->where('uid', $item['child'])->get()->toArray()[0]['total'] ?: 0;
                $result[$item['member_id']]['second_order_amount'] = intval(DB::table('yz_order')->select(DB::raw('sum(price) as money'))->where('uid', $item['child'])->get()->toArray()[0]['money']) ?: 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity']+$result[$item['member_id']]['second_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount']+$result[$item['member_id']]['second_order_amount'];
            }

            $member_3 = DB::table('yz_member_children')->select('member_id', DB::raw('group_concat(child_id) as child'))->where('level', 3)->groupBy('member_id')->get()->toArray();
            foreach ($member_3 as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['third_order_quantity'] = DB::table('yz_order')->select(DB::raw('count(id) as total'))->where('uid', $item['child'])->get()->toArray()[0]['total'] ?: 0;
                $result[$item['member_id']]['third_order_amount'] = intval(DB::table('yz_order')->select(DB::raw('sum(price) as money'))->where('uid', $item['child'])->get()->toArray()[0]['money']) ?: 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity']+$result[$item['member_id']]['second_order_quantity']+$result[$item['member_id']]['third_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount']+$result[$item['member_id']]['second_order_amount']+$result[$item['member_id']]['third_order_amount'];
            }

            $memberModel = new MemberLowerOrder();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }

        }
    }
}