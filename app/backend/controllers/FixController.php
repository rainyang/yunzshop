<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/8
 * Time: 下午6:52
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\models\Income;
use app\common\models\Order;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\models\CommissionOrder;

class FixController extends BaseController
{
    public function handleCommissionOrder()
    {

        $handle = 0;
        $success = 0;

        $waitCommissionOrder = CommissionOrder::uniacid()->whereStatus(0)->get();



        if (!$waitCommissionOrder->isEmpty()) {

            foreach ($waitCommissionOrder as $key => $commissionOrder) {

                $orderModel = Order::uniacid()->whereId($commissionOrder->ordertable_id)->first();

                if ($orderModel->status == 3) {

                    $handle += 1;
                    $commissionOrder->status = 1;

                    if ($commissionOrder->save()) {
                        $success += 1;
                    }
                }
                unset($orderModel);
            }
        }

        echo "分销订单未结算总数：{$waitCommissionOrder->count()}，已完成订单数：{$handle}, 执行成功数：{$success}";
    }

    public function fixTeam()
    {
        $search_date = strtotime('2018-10-25 12:00:00');
        $error = [];
        $tmp      = [];
        $pos      = [];

        $res = DB::table('yz_team_dividend as t')
            ->select(['t.id' , 'o.id as orderid', 'o.uid', 't.order_sn', 't.member_id', 't.status'])
            ->join('yz_order as o', 'o.order_sn', '=', 't.order_sn')
            ->where('t.created_at', '>', $search_date)
            ->orderBy('t.id', 'asc')
            ->get();

        if (!$res->isEmpty()) {
            foreach ($res as $key => $rows) {
                if (!$tmp[$rows['orderid']]) {
                    // $pos = [$rows->member_id => $key];

                    $tmp[$rows['orderid']] = [
                        'id'    => $rows['id'],
                        'order_id' => $rows['orderid'],
                        'uid' => $rows['uid'],
                        'order_sn' => $rows['order_sn'],
                        'parent_id' => $rows['member_id'],
                        'status' => $rows['status'],
                    ];

                    file_put_contents(storage_path('logs/team_fix.log'), print_r($tmp, 1), FILE_APPEND);
                } else {
//                    $k = $pos[$rows->member_id];
//                    $tmp[$k]['member_id'][] = $rows->member_id;
                }
            }
        }

        //订单会员->关系链 不匹配
        foreach ($tmp as $k => $v) {
            $total = DB::table('yz_member')
                ->where('member_id', '=', $v['uid'])
                ->where('parent_id', '=', $v['parent_id'])
                ->count();

            if (0 == $total) {
                $error[] = $v;

                file_put_contents(storage_path('logs/team_fix_error.log'), print_r($v, 1), FILE_APPEND);
            }
        }

        collect($error)->each(function ($item) {
            if (0 == $item['status']) {
                $model = Order::find($item['order_id']);

                if (!is_null($model)) {
                    DB::transaction(function () use ($item, $model) {
                        DB::table('yz_team_dividend')
                            ->where('order_sn', '=', $item['order_sn'])
                            ->delete();

                        DB::table('yz_order_plugin_bonus')
                            ->where('order_id', '=', $item['order_id'])
                            ->where('table_name', '=', 'yz_team_dividend')
                            ->delete();

                        (new \Yunshop\TeamDividend\Listener\OrderCreatedListener)->fixOrder($model);

                        file_put_contents(storage_path('logs/team_fix_del.log'), print_r($item, 1), FILE_APPEND);
                    });
                }
            }
        });

        echo '数据修复ok';
    }

    public function fixArea()
    {
        $search_date = strtotime('2018-10-25 12:00:00');
        $error = [];
        $tmp      = [];

        $res = DB::table('yz_area_dividend as t')
            ->select(['t.id' , 'o.id as orderid', 'o.uid', 't.order_sn', 't.member_id', 't.status'])
            ->join('yz_order as o', 'o.order_sn', '=', 't.order_sn')
            ->where('t.created_at', '>', $search_date)
            ->orderBy('t.id', 'asc')
            ->get();

        if (!$res->isEmpty()) {
            foreach ($res as $key => $rows) {
                if (!$tmp[$rows['orderid']]) {
                    // $pos = [$rows->member_id => $key];

                    $tmp[$rows['orderid']] = [
                        'id'    => $rows['id'],
                        'order_id' => $rows['orderid'],
                        'uid' => $rows['uid'],
                        'order_sn' => $rows['order_sn'],
                        'parent_id' => $rows['member_id'],
                        'status' => $rows['status'],
                    ];

                    file_put_contents(storage_path('logs/area_fix.log'), print_r($tmp, 1), FILE_APPEND);
                }
            }
        }

//        //订单会员->关系链 不匹配
//        foreach ($tmp as $k => $v) {
//            $total = DB::table('yz_member')
//                ->where('member_id', '=', $v['uid'])
//                ->where('parent_id', '=', $v['parent_id'])
//                ->count();
//
//            if (0 == $total) {
//                $error[] = $v;
//
//                file_put_contents(storage_path('logs/area_fix_error.log'), print_r($v, 1), FILE_APPEND);
//            }
//        }

        collect($tmp)->each(function ($item) {
            if (0 == $item['status']) {
                $model = Order::find($item['order_id']);

                DB::transaction(function () use ($item, $model) {
                    DB::table('yz_area_dividend')
                        ->where('order_sn', '=', $item['order_sn'])
                        ->delete();

                    DB::table('yz_order_plugin_bonus')
                        ->where('order_id', '=', $item['order_id'])
                        ->where('table_name', '=', 'yz_area_dividend')
                        ->delete();

                    (new \Yunshop\AreaDividend\Listener\OrderCreatedListener)->fixOrder($model);

                    file_put_contents(storage_path('logs/area_fix_del.log'), print_r($item, 1), FILE_APPEND);
                });
            }
        });

        echo '数据修复ok';
    }

    public function fixIncome()
    {
        $count = 0;
        $income = Income::whereBetween('created_at', [1539792000,1541433600])->get();
        foreach ($income as $value) {
            $pattern1 = '/\\\u[\d|\w]{4}/';
            preg_match($pattern1, $value->detail, $exists);
            if (empty($exists)) {
                $pattern2 = '/(u[\d|\w]{4})/';
                $value->detail = preg_replace($pattern2, '\\\$1', $value->detail);
                $value->save();
                $count++;
            }
        }
        echo "修复了{$count}条";
    }
}