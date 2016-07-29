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
namespace controller\api\order;
class ChangePrice extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        //$api->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();
//缩略图,标题,价格 数量,小计, 应收,运费,实入
        $order_model = new \model\api\order();
        $order_price = $order_model->getInfo(array(
            'id' => $para['order_id'],
            'uniacid' => $para['uniacid'],
        ), 'price as total_price,(`price` - `dispatchprice`) as price,dispatchprice');
        $order_goods = pdo_fetchall("select g.title,g.thumb,og.total,og.price,og.total*og.price as total_price from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $para["uniacid"],
            ":orderid" => $para["order_id"]
        ));
        //$order_goods = $order_model->getOrderGoods($para["order_id"], $para["uniacid"]);


        $res = compact('order_goods', 'order_price');
        dump($res);
        $this->returnSuccess($res);
    }
}