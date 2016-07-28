<?php
/**
 * 管理后台APP API订单状态改变
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\order;
class ChangeStatus extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        $this->ca("order.op.pay");

    }

    private function confirmpay($order)
    {
        global $_W;
        if ($order["status"] > 1) {
            $this->returnError("订单已付款，不需重复付款！");
        }
        $virtual = p("virtual");
        if (!empty($order["virtual"]) && $virtual) {
            $virtual->pay($order);
        } else {
            /*pdo_update("sz_yi_order", array(
            "status" => 1,
            "paytype" => 11,
            "paytime" => time()
            ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
            ));
            m("order")->setStocksAndCredits($order["id"], 1);
            m("notice")->sendOrderMessage($order["id"]);
            if (p("coupon") && !empty($order["couponid"])) {
            p("coupon")->backConsumeCoupon($order["id"]);
            }
            if (p("commission")) {
            p("commission")->checkOrderPay($order["id"]);
            }*/
            $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':module' => 'sz_yi',
                ':tid' => $order['ordersn']
            ));
            pdo_update("sz_yi_order", array('paytype' => '11'), array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
            $ret = array();
            $ret['result'] = 'success';
            $ret['from'] = 'return';
            $ret['tid'] = $log['tid'];
            $ret['user'] = $order['openid'];
            $ret['fee'] = $order['price'];
            $ret['weid'] = $_W['uniacid'];
            $ret['uniacid'] = $_W['uniacid'];
            $payresult = m('order')->payResult($ret);
        }
        plog("order.op.pay", "订单确认付款 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
        $this->returnSuccess("确认订单付款操作成功！");
    }
}

