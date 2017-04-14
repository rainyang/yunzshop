<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/7
 * Time: 下午2:59
 */

namespace app\backend\modules\order\models;

class Order extends \app\common\models\Order
{
    private static function format($builder,$pageSize){
        $list['total_price'] = $builder->sum('price');
        $list += $builder->orderBy('id','desc')->paginate($pageSize)->appends(['button_models'])->toArray();
        return $list;
    }
    public static function getAllOrders($search, $pageSize)
    {
        $builder = Order::orders($search);
        return self::format($builder,$pageSize);
    }

    public static function getWaitPayOrders($search, $pageSize)
    {
        $builder = Order::orders($search, $pageSize)->waitPay();
        return self::format($builder,$pageSize);
    }

    public static function getWaitSendOrders($search, $pageSize)
    {
        $builder = Order::orders($search, $pageSize)->waitSend();
        return self::format($builder,$pageSize);
    }

    public static function getWaitReceiveOrders($search, $pageSize)
    {
        $builder = Order::orders($search, $pageSize)->waitReceive();
        return self::format($builder,$pageSize);

    }

    public static function getCompletedOrders($search, $pageSize)
    {
        $builder = Order::orders($search, $pageSize)->completed();
        return self::format($builder,$pageSize);
    }

    /**
     * @param $search
     * @param $pageSize
     * @return mixed
     * 获取退换货订单
     */
    public static function getRefundOrders($search, $pageSize)
    {
        $builder = Order::orders($search, $pageSize)->refund();
        $list['total_price'] = $builder->sum('price');
        $list += $builder->paginate($pageSize)->appends(['button_models'])->toArray();
        return $list;
    }

    //订单导出订单数据
    public static function getExportOrders($search)
    {
        $builder = Order::exportOrders($search);
        $orders = $builder->get()->toArray();
        return $orders;
    }

    public function scopeExportOrders($search)
    {
        $order_builder = Order::search($search);

        $orders = $order_builder->with([
            'belongsToMember' => self::member_builder(),
            'hasManyOrderGoods' => self::order_goods_builder(),
            'hasOneDispatchType',
            'hasOnePayType',
            'address',
            'hasOneOrderRemark',
            'hasOneOrderExpress',
            'hasOnePayType'
        ]);
        return $orders;
    }

    public function scopeOrders($order_builder, $search)
    {
        $order_builder->search($search);

        $orders = $order_builder->with([
            'belongsToMember' => self::member_builder(),
            'hasManyOrderGoods' => self::order_goods_builder(),
            'hasOneDispatchType',
            'hasOnePayType',
            'address',
            'hasOnePayType'
        ]);
        return $orders;
    }

    private static function member_builder()
    {
        return function ($query) {
            return $query->select(['uid', 'mobile', 'nickname', 'realname']);
        };
    }

    private static function order_goods_builder()
    {
        return function ($query) {
            $query->select(['id', 'order_id', 'goods_id', 'goods_price', 'total', 'price', 'thumb', 'title', 'goods_sn']);
        };
    }

    public function scopeIsPlugin($query)
    {
        return $query->where('is_plugin', 0);
    }

    public function scopeSearch($order_builder, $params)
    {
        $order_builder->isPlugin();
        if (array_get($params, 'ambiguous.field', '') && array_get($params, 'ambiguous.string', '')) {
            //订单
            if ($params['ambiguous']['field'] == 'order') {
                call_user_func(function () use (&$order_builder, $params) {
                    list($field, $value) = explode(':', $params['ambiguous']['string']);
                    if (isset($value)) {
                        return $order_builder->where($field, $value);
                    } else {
                        return $order_builder->searchLike($params['ambiguous']['string']);
                    }
                });


            }
            //用户
            if ($params['ambiguous']['field'] == 'member') {
                call_user_func(function () use (&$order_builder, $params) {
                    list($field, $value) = explode(':', $params['ambiguous']['string']);
                    if (isset($value)) {
                        return $order_builder->where($field, $value);
                    } else {
                        return $order_builder->whereHas('belongsToMember', function ($query) use ($params) {
                            return $query->searchLike($params['ambiguous']['string']);
                        });
                    }
                });

            }
            //订单商品
            if ($params['ambiguous']['field'] == 'order_goods') {
                $order_builder->whereHas('hasManyOrderGoods', function ($query) use ($params) {
                    $query->searchLike($params['ambiguous']['string']);
                });
            }
        }
        //支付方式
        if (array_get($params, 'pay_type', '')) {
            $order_builder->where('pay_type_id', $params['pay_type']);
        }
        //操作时间范围

        if (array_get($params, 'time_range.field', '') && array_get($params, 'time_range.start', 0) && array_get($params, 'time_range.end', 0)) {
            $range = [strtotime($params['time_range']['start']), strtotime($params['time_range']['end'])];
            $order_builder->whereBetween($params['time_range']['field'], $range);
        }
        return $order_builder;
    }
}