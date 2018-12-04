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

            $order = DB::table('yz_order')->select('uid','price')->where('uniacid', \YunShop::app()->uniacid)->get();
            $member_1 = DB::select('select member_id, group_concat(child_id) as child,level from ims_yz_member_children where level =1' . ' and uniacid =' . \YunShop::app()->uniacid . ' group by member_id');
            foreach ($member_1 as $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['first_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['first_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
                $result[$item['member_id']]['second_order_quantity'] = 0;
                $result[$item['member_id']]['second_order_amount'] = 0;
                $result[$item['member_id']]['third_order_quantity'] = 0;
                $result[$item['member_id']]['third_order_amount'] = 0;
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount'];
            }
//        dd($result);

            $member_2 = DB::select('select member_id, group_concat(child_id) as child,level from ims_yz_member_children where level =2' . ' and uniacid =' . \YunShop::app()->uniacid . ' group by member_id');
            foreach ($member_2 as $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['second_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['second_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
                $result[$item['member_id']]['team_order_quantity'] = $result[$item['member_id']]['first_order_quantity']+$result[$item['member_id']]['second_order_quantity'];
                $result[$item['member_id']]['team_order_amount'] = $result[$item['member_id']]['first_order_amount']+$result[$item['member_id']]['second_order_amount'];
            }
//        dd($result);

            $member_3 = DB::select('select member_id, group_concat(child_id) as child,level from ims_yz_member_children where level =3' . ' and uniacid =' . \YunShop::app()->uniacid . ' group by member_id');
            foreach ($member_3 as $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['third_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['third_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
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