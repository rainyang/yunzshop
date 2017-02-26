<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午4:45
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $table = 'yz_order';

    //获取支付单号
    public static function getOrdersnGeneral($openid, $uniacid, $orderid)
    {
        $order = self::select('order_sn_general')
            ->where('open_id', '=', $openid)
            ->where('uniac_id', '=', $uniacid)
            ->where('id', '=', $orderid)
            ->first();
        return $order['ordersn_general'];
    }

    //获取所有订单
    public static function getOrders($ordersn_general, $uniacid, $openid)
    {
        $orders = self::where('order_sn_general', '=', $ordersn_general)
            ->where('uniac_id', '=', $uniacid)
            ->where('open_id', '=', $openid)
            -get();
        return $orders;
    }

    //获取log
    public static function getLog($ordersn_general, $uniacid)
    {
        $log = self::select('*')
            ->where('tid', '=', $ordersn_general)
            ->where('uniac_id', '=', $uniacid)
            ->where('module', '=', 'sz_yi')
            ->first();
        return $log;
    }

    //删除log
    public static function deleteLog($plid)
    {
        self::where('plid', '=', $plid)
            ->delete();
    }

    //插入log 并返回 id
    public static function insertLog($log)
    {
        self::insert($log);
        $id = self::insertGetId();
        return $id;
    }

    //查询订单的所有商品
    public static function getOrderGoods($condition, $uniacid)
    {
        $order_goods = DB::table('yz_order_goods')
            ->join('yz_goods', function($join)
            {
                $join->on('og.goodsid', '=', 'g.id');
            })->select('og.id', 'g.title', 'g.type', 'og.goodsid', 'og.optionid', 'g.thumb', 'g.total as stock', 'og.total as buycount', 'g.status', 'g.deleted', 'g.maxbuy', 'g.usermaxbuy', 'g.istime', 'g.timestart', 'g.timeend', 'g.buylevels', 'g.buygroups')
            ->where($condition)
            ->where('og.uniacid', '=', $uniacid)
            ->get();
        return $order_goods;
    }

    //购买个数
    public static function getBuyCount($goodsid, $uniacid, $openid)
    {
        $order_goods_count = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(
            ':goodsid' => $goodsid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        return $order_goods_count;
    }

    //支付修改订单
    public static function payUpdateOrder($condition, $uniacid, $pay_type)
    {
        self::update(['paytype' => $pay_type])
            ->where($condition)
            ->where('uniacid', '=', $uniacid);
    }
}