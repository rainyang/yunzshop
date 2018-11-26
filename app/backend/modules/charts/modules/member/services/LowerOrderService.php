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
            $member_1 = DB::select('select member_id, group_concat(child_id) as child,level from ims_yz_member_children where level =1' . ' and uniacid =' . $uniacid . ' group by member_id');

            foreach ($member_1 as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['first_order_quantity'] = DB::select('select count(id) as total from ims_yz_order where uid in (' . $item['child'] . ')')[0]['total'] ?: 0;
                $result[$item['member_id']]['first_order_amount'] = intval(DB::select('select sum(price) as money from ims_yz_order where uid in (' . $item['child'] . ')')[0]['money']) ?: 0;
                $result[$item['member_id']]['second_order_quantity'] = 0;
                $result[$item['member_id']]['second_order_amount'] = 0;
                $result[$item['member_id']]['third_order_quantity'] = 0;
                $result[$item['member_id']]['third_order_amount'] = 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount'];
            }
//dd($result);
            $member_2 = DB::select('select member_id, group_concat(child_id) as child,level from ims_yz_member_children where level =2' . ' and uniacid =' . $uniacid . ' group by member_id');
            foreach ($member_2 as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['second_order_quantity'] = DB::select('select count(id) as total from ims_yz_order where uid in (' . $item['child'] . ')')[0]['total'] ?: 0;
                $result[$item['member_id']]['second_order_amount'] = intval(DB::select('select sum(price) as money from ims_yz_order where uid in (' . $item['child'] . ')')[0]['money']) ?: 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity']+$result[$item['member_id']]['second_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount']+$result[$item['member_id']]['second_order_amount'];
            }
            $member_3 = DB::select('select member_id, group_concat(child_id) as child,level from ims_yz_member_children where level =3' . ' and uniacid =' . $uniacid . ' group by member_id');
            foreach ($member_3 as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['third_order_quantity'] = DB::select('select count(id) as total from ims_yz_order where uid in (' . $item['child'] . ')')[0]['total'] ?: 0;
                $result[$item['member_id']]['third_order_amount'] = intval(DB::select('select sum(price) as money from ims_yz_order where uid in (' . $item['child'] . ')')[0]['money']) ?: 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity']+$result[$item['member_id']]['second_order_quantity']+$result[$item['member_id']]['third_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount']+$result[$item['member_id']]['second_order_amount']+$result[$item['member_id']]['third_order_amount'];
            }
//        dd($result);
            $memberModel = new MemberLowerOrder();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }

        }
    }
}