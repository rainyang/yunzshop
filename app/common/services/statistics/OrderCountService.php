<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 9:57
 */

namespace app\common\services\statistics;

use app\common\models\MemberShopInfo;
use app\common\models\Order;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;
use app\Jobs\StatisticsJob;
use app\common\models\statistic\OrderCountModel;

class OrderCountService
{
    public function statistics()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $memberDoesntHave = MemberShopInfo::select('member_id', 'uniacid', 'parent_id')->whereHas('hasOneMember')->whereDoesntHave('hasOneOrder')->get()->toArray();
            $order_count_model = new OrderCountModel();
            $member_count_data = [];
            foreach ($memberDoesntHave as $item) {
                $member_count_data['member_id'] = $item['member_id'];
                $member_count_data['parent_id'] = $item['parent_id'] ?:0;
                $member_count_data['uniacid'] = $item['uniacid'];
                $member_count_data['total_quantity'] = 0;
                $member_count_data['total_amount'] = 0;
                $member_count_data['total_pay_quantity'] = 0;
                $member_count_data['total_pay_amount'] = 0;
                $member_count_data['total_complete_quantity'] = 0;
                $member_count_data['total_complete_amount'] = 0;

                $order_count_model->updateOrCreate(['member_id' => $item['member_id']],$member_count_data);
            }

            $memberHasAll = Order::select(DB::raw('count(uid) as order_count,sum(price) as order_amount, uid'))
                ->whereHas('hasOneMemberShopInfo')
                ->with(['hasOneMemberShopInfo' => function($query) {
                    return $query->select('member_id', 'parent_id', 'uniacid');
                }])
                ->groupBy('uid')
                ->get()->toArray();

            $order_all_data = [];
            foreach ($memberHasAll as $key => $item) {
                $order_all_data[$key]['member_id'] = $item['uid'];
                $order_all_data[$key]['parent_id'] = $item['has_one_member_shop_info']['parent_id'];
                $order_all_data[$key]['uniacid'] = $item['has_one_member_shop_info']['uniacid'];
                $order_all_data[$key]['total_quantity'] = $item['order_count'];
                $order_all_data[$key]['total_amount'] = $item['order_amount'];
            }

            $order_pay_data = [];
            $pay_status = [1, 2, 3];
            $memberHasByPay = Order::select(DB::raw('count(uid) as total_pay_quantity,sum(price) as total_pay_amount, uid'))
                ->whereHas('hasOneMemberShopInfo')
                ->with(['hasOneMemberShopInfo' => function($query) {
                    return $query->select('member_id', 'parent_id', 'uniacid');
                }])
                ->whereBetween('status', $pay_status)
                ->groupBy('uid')
                ->get()->toArray();

            foreach ($memberHasByPay as $key => $item) {
                $order_pay_data[$key]['member_id'] = $item['uid'];
                $order_pay_data[$key]['parent_id'] = $item['has_one_member_shop_info']['parent_id'];
                $order_pay_data[$key]['uniacid'] = $item['has_one_member_shop_info']['uniacid'];
                $order_pay_data[$key]['total_pay_quantity'] = $item['total_pay_quantity'];
                $order_pay_data[$key]['total_pay_amount'] = $item['total_pay_amount'];
            }

            $order_complete_data = [];
            $memberHasByComplete = Order::select(DB::raw('count(uid) as total_complete_quantity,sum(price) as total_complete_amount, uid'))
                ->whereHas('hasOneMemberShopInfo')
                ->with(['hasOneMemberShopInfo' => function($query) {
                    return $query->select('member_id', 'parent_id', 'uniacid');
                }])
                ->where('status', 3)
                ->groupBy('uid')
                ->get()->toArray();

            foreach ($memberHasByComplete as $key => $item) {
                $order_complete_data[$key]['member_id'] = $item['uid'];
                $order_complete_data[$key]['parent_id'] = $item['has_one_member_shop_info']['parent_id'];
                $order_complete_data[$key]['uniacid'] = $item['has_one_member_shop_info']['uniacid'];
                $order_complete_data[$key]['total_complete_quantity'] = $item['total_complete_quantity'];
                $order_complete_data[$key]['total_complete_amount'] = $item['total_complete_amount'];
            }

            $order_all_model =  array_merge($order_all_data, $order_pay_data, $order_complete_data);

            $result = array_reduce($order_all_model, function ($last, $next) {
                $key = $next['member_id'];
                // 第一次存入数组
                if (! array_key_exists($key, $last)) {
                    $last[$key] = $next;
                    return $last;
                }
                // 之后的相加
                foreach ($next as $k => $val) {
                    // 需要合并的  key，比如 id 不用合并
                    if (in_array($k, ['total_quantity', 'total_amount','total_pay_quantity' , 'total_pay_amount', 'total_complete_amount', 'total_complete_quantity'])) {
                        // 可能原来的数组没有 total_complete_amount
                        if (! isset ($last[$key][$k])) {
                            $last[$key][$k] = 0;
                        }
                        $last[$key][$k] += $next[$k];
                    }
                }
                return $last;
            }, []);

            $orderModel = new OrderCountModel();
            $memberCountData = [];
            foreach ($result as $item) {
                $memberCountData['member_id'] = $item['member_id'];
                $memberCountData['parent_id'] = $item['parent_id'];
                $memberCountData['uniacid'] = $item['uniacid'];
                $memberCountData['total_quantity'] = $item['total_quantity']?:0;
                $memberCountData['total_amount'] = $item['total_amount']?:0;
                $memberCountData['total_pay_quantity'] = $item['total_pay_quantity']?:0;
                $memberCountData['total_pay_amount'] = $item['total_pay_amount']?:0;
                $memberCountData['total_complete_quantity'] = $item['total_complete_quantity']?:0;
                $memberCountData['total_complete_amount'] = $item['total_complete_amount']?:0;

                $orderModel->updateOrCreate(['member_id' => $item['member_id']],$memberCountData);
            }
        }
        dispatch(new StatisticsJob());
        return true;
    }
}