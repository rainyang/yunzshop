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
            $result = [];
            \Log::debug('--------执行-------', \YunShop::app()->uniacid);
            $order   = DB::table('yz_order')->select('uid','price')->where('status', '>=' ,1)->where('uniacid', \YunShop::app()->uniacid)->get();
            //$group   = DB::select('select DISTINCT(member_id) from '. DB::getTablePrefix() . 'yz_member_children where uniacid =' . \YunShop::app()->uniacid );
            $members = DB::select('select `member_id`, `child_id` as child, `level` from ' . DB::getTablePrefix() .'yz_member_children where uniacid =' . \YunShop::app()->uniacid);


            foreach ($members as $key => $value){
                $member_all[$value['member_id']]['child'] .= $value['child'].',';
            }
            foreach ($member_all as $key => &$value){
                $value['child'] = rtrim($value['child'], ',');
                $member_all[$key]['member_id'] = $key;
            }
            if(!$member_all){
                break;
            }

//            foreach ($group as $key => $group_member) {
//                $member_all[$key] = [
//                    'member_id' => $group_member['member_id'],
//                    'child'  => ''
//                ];
//
//                foreach ($members as $member_info) {
//                    if ($group_member['member_id'] == $member_info['member_id']) {
//                        $member_all[$key]['child'] .= $member_info['child'] . ',';
//                    }
//                }
//                $member_all[$key]['child'] =  rtrim($member_all[$key]['child'], ',');
//            }
//
            foreach ($member_all as $item){
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = \YunShop::app()->uniacid;
                $result[$item['member_id']]['team_order_quantity'] = $order->whereIn('uid', explode(',',$item['child']))->sum('goods_total');
                $result[$item['member_id']]['team_order_amount'] = $order->whereIn('uid', explode(',',$item['child']))->sum('price');
                $result[$item['member_id']]['team_count'] = count(explode(',',$item['child']));
                $result[$item['member_id']]['pay_count'] = $order->whereIn('uid', explode(',',$item['child']))->groupBy('uid')->count();
            }

            $memberModel = new TeamOrder();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }
        }
    }

}