<?php
/**
 * 管理后台APP API商品状态设置接口
 *
 * PHP version 5.6.15
 *
 * @package   商品模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\controller\order;
class ChangePrice extends \admin\api\YZ
{
    private $order_info;

    public function __construct()
    {
        parent::__construct();
        $para = $this->getPara();

        $order_model = new \admin\api\model\order();

        $this->order_info = $order_model->getInfo(array(
            'id' => $para['order_id'],
            'uniacid' => $para['uniacid'],
        ));
        //$api->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();
        //缩略图,标题,价格 数量,小计, 应收,运费,实入
        $order_info = $this->order_info;
        $order_price = array(
            'total_price' => $order_info['price'],
            'price' => $order_info['price'] - $order_info['dispatchprice'],
            'dispatchprice' => $order_info['dispatchprice']
        );

        $order_goods = pdo_fetchall("select og.id as order_goods_id,g.title,g.thumb,og.total,og.price,og.realprice,og.total*og.price as total_price from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $para["uniacid"],
            ":orderid" => $para["order_id"]
        ));
        //$order_goods = $order_model->getOrderGoods($para["order_id"], $para["uniacid"]);

        $res = array(
            'order_goods' => $order_goods,
            'order_price' => $order_price
        );
        dump($res);
        $this->returnSuccess($res);
    }

    public function confirm()
    {
        $para = $this->getPara();
//dump($para);exit;
        $order_info = $this->order_info;

        //changegoodsprice,uniacid,changedispatchprice
        $changegoodsprice = $para["changegoodsprice"];
        if (!is_array($changegoodsprice)) {
            $this->returnError("未找到改价内容!", '', "error");
        }
        $changeprice = 0;
        foreach ($changegoodsprice as $ogid => $change) {
            $changeprice += floatval($change);
        }
        $dispatchprice = floatval($para["changedispatchprice"]);
        if ($dispatchprice < 0) {
            $dispatchprice = 0;
        }
        $orderprice = $order_info["price"] + $changeprice;
        $changedispatchprice = 0;
        if ($dispatchprice != $order_info["dispatchprice"]) {
            $changedispatchprice = $dispatchprice - $order_info["dispatchprice"];
            $orderprice += $changedispatchprice;
        }
        if ($orderprice < 0) {
            $this->returnError("订单实际支付价格不能小于0元！", '', "error");
        }
        foreach ($changegoodsprice as $ogid => $change) {
            $og = pdo_fetch("select price,realprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1", array(
                ":ogid" => $ogid,
                ":uniacid" => $para["uniacid"]
            ));

            if (!empty($og)) {
                $realprice = $og["realprice"] + $change;
                if ($realprice < 0) {
                    $this->returnError("单个商品不能优惠到负数", '', "error");
                }
            }
        }
        $ordersn2 = $order_info["ordersn2"] + 1;
        if ($ordersn2 > 99) {
            $this->returnError("超过改价次数限额");
        }
        $orderupdate = array();
        if ($orderprice != $order_info["price"]) {
            $orderupdate["price"] = $orderprice;
            $orderupdate["ordersn2"] = $order_info["ordersn2"] + 1;
        }
        $orderupdate["changeprice"] = $order_info["changeprice"] + $changeprice;
        if ($dispatchprice != $order_info["dispatchprice"]) {
            $orderupdate["dispatchprice"] = $dispatchprice;
            $orderupdate["changedispatchprice"] += $changedispatchprice;
        }
        if (!empty($orderupdate)) {
            pdo_update("sz_yi_order", $orderupdate, array(
                "id" => $order_info["id"],
                "uniacid" => $para["uniacid"]
            ));
        }
        foreach ($changegoodsprice as $ogid => $change) {
            $og = pdo_fetch("select price,realprice,changeprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1", array(
                ":ogid" => $ogid,
                ":uniacid" => $para["uniacid"]
            ));
            if (!empty($og)) {
                $realprice = $og["realprice"] + $change;
                $changeprice = $og["changeprice"] + $change;
                pdo_update("sz_yi_order_goods", array(
                    "realprice" => $realprice,
                    "changeprice" => $changeprice
                ), array(
                    "id" => $ogid
                ));
            }
        }
        if (abs($changeprice) > 0) {
            $pluginc = p("commission");
            if ($pluginc) {
                $pluginc->calculate($order_info["id"], true);
            }
        }
        plog("order.op.changeprice", "订单号： {$order_info["ordersn"]} <br/> 价格： {$order_info["price"]} -> {$orderprice}");

        $order_model = new \admin\api\model\order();
        $this->order_info = $order_model->getInfo(array(
            'id' => $para['order_id'],
            'uniacid' => $para['uniacid'],
        ));
        $order_info = $order_model->getPriceInfo($order_info);
        $this->returnSuccess($order_info, "订单改价成功!");
    }
}