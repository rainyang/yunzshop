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
            $member_all = [];

            \Log::debug('--------执行-------', \YunShop::app()->uniacid);
            $order   = DB::table('yz_order')->select('uid','price')->where('status', 3)->where('uniacid', \YunShop::app()->uniacid)->get();
            $group   = DB::select('select `member_id` from '. DB::getTablePrefix() . 'yz_member_children where uniacid =' . \YunShop::app()->uniacid . ' group by member_id');
            $members = DB::select('select `member_id`, `child_id` as child, `level` from ' . DB::getTablePrefix() .'yz_member_children where uniacid =' . \YunShop::app()->uniacid);

            foreach ($group as $key => $group_member) {
                $member_1[$key] = $member_2[$key] = $member_3[$key] = $member_all[$key] = [
                    'member_id' => $group_member['member_id'],
                    'child'  => ''
                ];

                foreach ($members as $member_info) {
                    if ($group_member['member_id'] == $member_info['member_id']) {
                        $member_all[$key]['child'] .= $member_info['child'] . ',';

                        switch ($member_info['level']) {
                            case 1:
                                $member_1[$key]['child'] .= $member_info['child'] . ',';
                                break;
                            case 2:
                                $member_2[$key]['child'] .= $member_info['child'] . ',';
                                break;
                            case 3:
                                $member_3[$key]['child'] .= $member_info['child'] . ',';
                                break;
                        }

                    }
                }

                $member_all[$key]['child'] =  rtrim($member_all[$key]['child'], ',');
                $member_1[$key]['child'] =  rtrim($member_1[$key]['child'], ',');
                $member_2[$key]['child'] =  rtrim($member_2[$key]['child'], ',');
                $member_3[$key]['child'] =  rtrim($member_3[$key]['child'], ',');
            }



            //$member_1 = DB::select('select member_id, group_concat(child_id) as child from '.DB::getTablePrefix().'yz_member_children where level =1' . ' and uniacid =' . \YunShop::app()->uniacid . ' group by member_id,level');
            //$member_all = DB::select('select member_id, group_concat(child_id) as child from '.DB::getTablePrefix().'yz_member_children where uniacid =' . \YunShop::app()->uniacid . ' group by member_id');
            foreach ($member_1 as $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['first_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['first_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
                $result[$item['member_id']]['second_order_quantity'] = 0;
                $result[$item['member_id']]['second_order_amount'] = 0;
                $result[$item['member_id']]['third_order_quantity'] = 0;
                $result[$item['member_id']]['third_order_amount'] = 0;
                $result[$item['member_id']]['team_order_quantity'] = 0;
                $result[$item['member_id']]['team_order_amount'] = 0;
            }
//        dd($result);

            //$member_2 = DB::select('select member_id, group_concat(child_id) as child,level from '.DB::getTablePrefix().'yz_member_children where level =2' . ' and uniacid =' . \YunShop::app()->uniacid . ' group by member_id,level');
            foreach ($member_2 as $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['second_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['second_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
            }
//        dd($result);

            //$member_3 = DB::select('select member_id, group_concat(child_id) as child,level from '.DB::getTablePrefix().'yz_member_children where level =3' . ' and uniacid =' . \YunShop::app()->uniacid . ' group by member_id,level');
            foreach ($member_3 as $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['third_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['third_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
            }

            foreach ($member_all as $item){
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['team_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['team_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
            }
//        dd($result);

            $memberModel = new MemberLowerOrder();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }

        }
    }
}