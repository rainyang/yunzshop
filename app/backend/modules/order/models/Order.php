<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/7
 * Time: 下午2:59
 */

namespace app\backend\modules\order\models;

use app\backend\modules\order\services\OrderService;
use Illuminate\Database\Eloquent\Builder;

class Order extends \app\common\models\Order
{
    //订单导出订单数据
    public static function getExportOrders($search)
    {
        $builder = Order::exportOrders($search);
        $orders = $builder->get()->toArray();
        return $orders;
    }

    public function hasManyOrderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function scopeExportOrders($query, $search)
    {
        $order_builder = $query->search($search);

        $orders = $order_builder->with([
            'belongsToMember' => self::memberBuilder(),
            'hasManyOrderGoods' => self::orderGoodsBuilder(),
            'hasOneDispatchType',
            'address',
            'hasOneOrderRemark',
            'express',
            'hasOnePayType',
            'hasOneOrderPay'
        ]);
        return $orders;
    }

    public function scopeOrders($order_builder, $search)
    {
        $order_builder->search($search);

        $orders = $order_builder->with([
            'belongsToMember' => self::memberBuilder(),
            'hasManyOrderGoods' => self::orderGoodsBuilder(),
            'hasOneDispatchType',
            'hasOnePayType',
            'address',
            'express',
            'hasOneRefundApply' => self::refundBuilder(),
            'hasOneOrderRemark',
            'hasOneOrderPay'

        ]);
        return $orders;
    }
    /**
     * 获取用户消费总额
     *
     * @param $uid
     * @return mixed
     */
    public static function getCostTotalPrice($uid)
    {
        return self::where('status', '>=', 1)
            ->where('status', '<=', 3)
            ->where('uid', $uid)
            ->sum('price');
    }

    /**
     * 获取用户消费次数
     *
     * @param $uid
     * @return mixed
     */
    public static function getCostTotalNum($uid)
    {
        return self::where('status','>=', 1)
            ->Where('status','<=', 3)
            ->where('uid', $uid)
            ->count('id');
    }
    private static function refundBuilder()
    {
        return function ($query) {
            return $query->with('returnExpress')->with('resendExpress');
        };
    }

    private static function memberBuilder()
    {
        return function ($query) {
            return $query->select(['uid', 'mobile', 'nickname', 'realname','avatar']);
        };
    }

    private static function orderGoodsBuilder()
    {
        return function ($query) {
            $query->orderGoods();
        };
    }

    public function scopeSearch($order_builder, $params)
    {
        if (array_get($params, 'ambiguous.field', '') && array_get($params, 'ambiguous.string', '')) {
            //订单.支付单号
            if ($params['ambiguous']['field'] == 'order') {
                call_user_func(function () use (&$order_builder, $params) {
                    list($field, $value) = explode(':', $params['ambiguous']['string']);
                    if (isset($value)) {
                        return $order_builder->where($field, $value);
                    } else {
                        return $order_builder->where(function ($query)use ($params){
                            $query->searchLike($params['ambiguous']['string']);

                            $query->orWhereHas('hasOneOrderPay', function ($query) use ($params) {
                                $query->where('pay_sn','like',"%{$params['ambiguous']['string']}%");
                            });
                        });

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

    public static function getOrderDetailById($order_id)
    {
        return self::orders()->with(['deductions','coupons'])->find($order_id);
    }

    public static function boot()
    {
        parent::boot();

//        static::addGlobalScope(function (Builder $builder) {
//            $builder->isPlugin();
//        });
    }
}