<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 19:00
 */

namespace app\backend\modules\charts\modules\order\services;


use app\backend\modules\charts\models\OrderStatistics;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;

class OrderStatisticsService
{
    public function orderStatistics()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $uniacid = \YunShop::app()->uniacid;
            $yzModel = DB::select('select member_id from ims_yz_member where uniacid=' . $uniacid . ' order by member_id');

            foreach ($yzModel as $k => $item) {
                $result[$item['member_id']]['uid'] = $item['member_id'];
                $result[$item['member_id']]['uniacid'] = $uniacid;
                $result[$item['member_id']]['total_quantity'] = DB::select('select count(id) as total from ims_yz_order where uid=' . $item['member_id'])[0]['total'] ?: 0;
                $result[$item['member_id']]['total_amount'] = DB::select('select sum(price) as money from ims_yz_order where uid=' . $item['member_id'])[0]['money'] ?: 0;
            }

            foreach ($yzModel as $item) {
                $result[$item['member_id']]['total_pay_quantity'] = DB::select('select count(id) as total from ims_yz_order where uid=' . $item['member_id'] . ' and status in (1,2,3)')[0]['total'] ?: 0;
                $result[$item['member_id']]['total_pay_amount'] = DB::select('select sum(price) as money from ims_yz_order where uid=' . $item['member_id'] . ' and status in (1,2,3)')[0]['money'] ?: 0;
            }

            foreach ($yzModel as $item) {
                $result[$item['member_id']]['total_complete_quantity'] = DB::select('select count(id) as total from ims_yz_order where uid=' . $item['member_id'] . ' and status=3')[0]['total'] ?: 0;
                $result[$item['member_id']]['total_complete_amount'] = DB::select('select sum(price) as money from ims_yz_order where uid=' . $item['member_id'] . ' and status=3')[0]['money'] ?: 0;
            }

//            dd($result);
            $memberModel = new OrderStatistics();
            foreach ($result as $item) {
                $memberModel->updateOrcreate(['uid' => $item['uid']], $item);
            }
        }

    }
}