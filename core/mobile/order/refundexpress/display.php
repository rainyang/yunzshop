<?php
namespace mobile\order;
class Display extends Base{
    public function index(){
        $orderid = $this->getOrderId();
        $uniacid = $this->getUniacid();
        $openid = $this->getOpenid();
        $order = pdo_fetch("select refundid from " . tablename("sz_yi_order") . " where id=:id and uniacid=:uniacid and openid=:openid limit 1", array(":id" => $orderid, ":uniacid" => $uniacid, ":openid" => $openid));
        if (empty($order)) {
            return show_json(0);
        }
        $refundid = $order["refundid"];
        $refund = pdo_fetch("select * from " . tablename("sz_yi_order_refund") . " where id=:id and uniacid=:uniacid  limit 1", array(":id" => $refundid, ":uniacid" => $uniacid));
        $set = set_medias(m("common")->getSysset("shop"), "logo");
        return show_json(1, array("order" => $order, "refund" => $refund, "set" => $set));
    }
}