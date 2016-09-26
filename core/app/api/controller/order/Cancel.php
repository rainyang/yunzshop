<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;

class Cancel extends YZ
{
    public function index()
    {
        $orderid = intval($_GPC['orderid']);
        $order   = pdo_fetch('select id,ordersn,openid,status,deductcredit,deductprice,deductyunbi,deductyunbimoney,couponid from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
            ':id' => $orderid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        if (empty($order)) {
            show_json(0, '订单未找到!');
        }
        if ($order['status'] != 0) {
            show_json(0, '订单已支付，不能取消!');
        }
        pdo_update('sz_yi_order', array(
            'status' => -1,
            'canceltime' => time()
        ), array(
            'id' => $order['id'],
            'uniacid' => $uniacid
        ));
        m('notice')->sendOrderMessage($orderid);
        if ($order['deductprice'] > 0) {
            $shop = m('common')->getSysset('shop');
            m('member')->setCredit($order['openid'], 'credit1', $order['deductcredit'], array(
                '0',
                $shop['name'] . "购物返还抵扣积分 积分: {$order['deductcredit']} 抵扣金额: {$order['deductprice']} 订单号: {$order['ordersn']}"
            ));
        }
        if ($order['deductyunbimoney'] > 0) {
            $shop = m('common')->getSysset('shop');
            p('yunbi')->setVirtualCurrency($order['openid'],$order['deductyunbi']);
            //虚拟币抵扣记录
            $data_log = array(
                'id'           => $member['id'],
                'openid'        => $openid,
                'credittype'    => 'virtual_currency',
                'money'         => $order['deductyunbi'],
                'remark'        => "购物返还抵扣".$yunbiset['yunbi_title']." ".$yunbiset['yunbi_title'].": {$order['deductyunbi']} 抵扣金额: {$order['deductyunbimoney']} 订单号: {$order['ordersn']}"
            );
        }
        p('yunbi')->addYunbiLog($uniacid,$data_log,'4');
        if (p('coupon') && !empty($order['couponid'])) {
            p('coupon')->returnConsumeCoupon($orderid);
        }
        $this->returnSuccess();
    }

}

