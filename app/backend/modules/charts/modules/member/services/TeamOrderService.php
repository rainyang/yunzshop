<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/5/6
 * Time: 18:30
 */

namespace app\backend\modules\charts\modules\member\services;

use app\backend\modules\charts\modules\member\models\TeamOrder;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;

class TeamOrderService
{
    public function memberOrder()
    {
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $member_all = [];
            $member_1 = [];
            $member_2 = [];
            $member_3 = [];
            $result = [];
            \Log::debug('--------执行-------', \YunShop::app()->uniacid);
            $order   = DB::table('yz_order')->select('uid','price')->where('status', '>=' ,1)->where('uniacid', \YunShop::app()->uniacid)->get();
            $group   = DB::select('select `member_id` from '. DB::getTablePrefix() . 'yz_member_children where uniacid =' . \YunShop::app()->uniacid . ' group by member_id');
            $members = DB::select('select `member_id`, `child_id` as child, `level` from ' . DB::getTablePrefix() .'yz_member_children where uniacid =' . \YunShop::app()->uniacid);
            foreach ($group as $key => $group_member) {
                $member_all[$key] = [
                    'member_id' => $group_member['member_id'],
                    'child'  => ''
                ];

                foreach ($members as $member_info) {
                    if ($group_member['member_id'] == $member_info['member_id']) {
                        $member_all[$key]['child'] .= $member_info['child'] . ',';
                    }
                }
                $member_all[$key]['child'] =  rtrim($member_all[$key]['child'], ',');
            }
            foreach ($member_all as $item){
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['team_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->count();
                $result[$item['member_id']]['team_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
                $res = explode(',',$item['child']);
                $pay_count = 0;
                foreach($res as $key => $value){
                    $count = $order->where('uid',$value)->count();
                    if($count >= 1){
                        $pay_count += 1;
                    }
                }
                $result[$item['member_id']]['team_count'] = count(explode(',',$item['child']));
                $result[$item['member_id']]['pay_count'] = $pay_count;
            }
            $memberModel = new TeamOrder();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }
        }
    }

}