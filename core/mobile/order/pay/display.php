<?php
namespace mobile\order\pay;

class Display extends Dase
{

    private function getLog($key)
    {
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') .
            ' WHERE `uniacid` = :uniacid AND `module` = :module AND `tid` = :tid limit 1',
            array(
                ':uniacid'  => $this->getUniacid(),
                ':module'   => 'sz_yi',
                ':tid'      => $this->getOrdersnGeneral()
            )
        );
        if (isset($key)) {
            return $log[$key];
        }
        return $log;
    }

    private function getLogId()
    {
        if (empty($this->getLog())) {
            $log = array(
                'uniacid' => $this->getUniacid(),
                'openid' => $this->getOpenid,
                'module' => "sz_yi",
                'tid' => $this->getOrdersnGeneral(),
                'fee' => $this->getOrder('price'),
                'status' => 0
            );
            pdo_insert('core_paylog', $log);
            $plid = pdo_insertid();
            return $plid;
        } else {
            return $this->getLog('plid');
        }
    }

    private function getAppAliPay()
    {
        $app_alipay = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('app_alipay') == 1) {
            $app_alipay['success'] = true;
        }
        return $app_alipay;
    }

    private function getCreditPay()
    {
        $credit_pay = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('credit') == 1) {
            if ($this->getOrder['deductcredit2'] <= 0) {
                $credit_pay = array(
                    'success' => true,
                    'current' => m('member')->getCredit($this->getOpenid(),
                        'credit2')
                );
            }
        }
        return $credit_pay;
    }

    private function getAppWechatPay()
    {
        $app_wechat = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('app_weixin') == 1) {
            $app_wechat['success'] = true;
        }
    }

    private function getPatmentSet($key)
    {
        $result = uni_setting($this->getUniacid(), array(
            'payment'
        ));
        if (isset($key)) {
            $data = explode('.', $key);
            foreach ($data as $v) {
                $result = $result[$v];
            }
            return $result[$key];
        }
        return $result;
    }

    private function getWechatPay()
    {
        $wechat = array(
            'success' => false,
            'qrcode' => false
        );
        if (is_weixin()) {
            if ($this->getPaySet() && ($this->getPaySet('weixin') == 1) && ($this->getPaySet('weixin_jie') != 1)) {
                if (is_array($this->getPatmentSet('wechat')) && $this->getPatmentSet('wechat.switch')) {
                    $wechat['success'] = true;
                    $wechat['weixin'] = true;
                    $wechat['weixin_jie'] = false;
                }
            }
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            if (($this->getPaySet() && ($this->getPaySet('weixin_jie') == 1) && !$wechat['success']) || ($this->getPaySet('weixin_jie') == 1)) {
                $wechat['success'] = true;
                $wechat['weixin_jie'] = true;
                $wechat['weixin'] = false;
            }
        }
        $wechat['jie'] = $this->getPaySet('weixin_jie');
        //扫码
        if (!isMobile() && $this->getPaySet() && $this->getPaySet('weixin') == 1) {
            if ($this->getPaySet() && $this->getPaySet('weixin') == 1) {
                if (is_array($this->getPatmentSet('wechat')) && $this->getPatmentSet('wechat.switch')) {
                    $wechat['qrcode'] = true;
                }
            }
        }
        return $wechat;
    }

    private function getAliPay()
    {
        $alipay = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('alipay') == 1) {
            if (is_array($this->getPatmentSet('alipay')) && $this->getPatmentSet('alipay.switch')) {
                $alipay['success'] = true;
            }
        }
    }

    private function getUnionPay()
    {
        $unionpay = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('unionpay') == 1) {
            if (is_array($this->getPatmentSet('unionpay')) && $this->getPatmentSet('unionpay.switch')) {
                $unionpay['success'] = true;
            }
        }
        return $unionpay;
    }

    private function getCash()
    {

        $cash = array(
            'success' => $this->getOrder('cash') == 1 && $this->getPaySet() && $this->getPaySet('cash') == 1 && $this->getOrder('dispatchtype') == 0
        );
        return $cash;
    }

    private function getStoreCash()
    {
        $storecash = array(
            'success' => $this->getOrder('cash') == 1 && $this->getPaySet() && $this->getPaySet('cash') == 1 && $this->getOrder('dispatchtype') == 1
        );
        return $storecash;
    }

    private function getYeePay()
    {
        //易宝支付
        $yeepay = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('yeepay') == 1) {
            $yeepay['success'] = true;
        }
        return $yeepay;
    }

    private function getGaohuitongPay()
    {
        //高汇通支付
        $gaohuitong = array(
            'success' => false
        );
        if (p('gaohuitong')) {
            $ght = pdo_fetch("select `switch` from " . tablename('sz_yi_gaohuitong') . ' where uniacid=:uniacid limit 1',
                array(
                    ':uniacid' => $this->getUniacid()
                ));
            if ($ght['switch']) {
                $gaohuitong['success'] = true;
            }
        }
        return $gaohuitong;
    }

    private function getPalPay()
    {
        $paypal = array(
            'success' => false
        );
        if ($this->getPaySet() && $this->getPaySet('paypalstatus') == 1) {
            $paypal['success'] = true;
        }
        return $paypal;
    }

    private function getReturnUrl()
    {
        $returnurl = urlencode($this->createMobileUrl('order/pay', array(
            'orderid' => $this->getOrderId()
        )));
        return $returnurl;
    }

    private function getOrderGoods()
    {
        $order_goods = pdo_fetchall('SELECT og.id, g.title, g.type, og.goodsid, og.optionid, g.thumb, g.total as stock, 
    og.total as buycount, g.status, g.deleted, g.maxbuy, g.usermaxbuy, g.istime, g.timestart, g.timeend, 
    g.buylevels, g.buygroups FROM  ' . tablename('sz_yi_order_goods') . ' og ' .
            ' LEFT JOIN ' . tablename('sz_yi_goods') . ' g 
    ON og.goodsid = g.id ' . ' WHERE ' . $this->getSqlCondtion() . ' AND og.uniacid = :uniacid ',
            array(
                ':uniacid' => $this->getUniacid()
            )
        );
        foreach ($order_goods as $key => &$value) {
            if (!empty($value['optionid'])) {
                $option = pdo_fetch("SELECT id, title, marketprice, goodssn, productsn, stock, virtual, weight FROM " .
                    tablename("sz_yi_goods_option") .
                    " WHERE id = :id AND goodsid = :goodsid AND uniacid = :uniacid  limit 1",
                    array(
                        ":uniacid" => $this->getUniacid(),
                        ":goodsid" => $value['goodsid'],
                        ":id" => $value['optionid']
                    )
                );
                if (!empty($option)) {
                    $value["optionid"] = $value['optionid'];
                    $value["optiontitle"] = $option["title"];
                    $value["marketprice"] = $option["marketprice"];
                    if (!empty($option["weight"])) {
                        $value["weight"] = $option["weight"];
                    }
                }
            }
        }
        unset($value);
        $order_goods = set_medias($order_goods, 'thumb');
        return $order_goods;
    }

    private function getSqlCondtion()
    {
        if (is_array($this->getOrderId())) {
            $orderids = implode(',', $this->getOrderId());
            $condtion = "og.orderid in ({$orderids})";
        } else {
            $condtion = "og.orderid={$this->getOrderId()}";
        }
        return $condtion;
    }

    function index()
    {
        global $_GPC;
        if (empty($this->getOrderId())) {
            return show_json(0, '参数错误!');
        }
        if (empty($this->getOrder)) {
            return show_json(0, '订单未找到!');
        }
        if ($this->getOrder('status') == -1) {
            return show_json(-1, '订单已关闭, 无法付款!');
        } elseif ($this->getOrder('status') >= 1) {
            return show_json(-1, '订单已付款, 无需重复支付!');
        }
        if (!empty($this->getLog()) && $this->getLog('status') != '0') {
            return show_json(-1, '订单已支付, 无需重复支付!');
        }
        if (!empty($this->getLog()) && $this->getLog('status') == '0') {
            pdo_delete('core_paylog', array(
                'plid' => $this->getLog('plid')
            ));
            $log = null;
        }

        $yunpay = array(
            'success' => false
        );
        if (p('yunpay')) {
            $yunpayinfo = p('yunpay')->getYunpay();
            if (isset($yunpayinfo) && @$yunpayinfo['switch']) {
                $yunpay['success'] = true;
            }
        }

        /*if (p('recharge')) {
            $order_goods_recharge = pdo_fetch('SELECT go.title, g.type, o.carrier, o.price FROM ' . tablename('sz_yi_order') .
                'o LEFT JOIN ' . tablename('sz_yi_order_goods') . ' og ' .
                ' ON o.id = og.orderid LEFT JOIN ' . tablename('sz_yi_goods') . ' g ' .
                ' ON og.goodsid = g.id LEFT JOIN' . tablename('sz_yi_goods_option') . ' go ' .
                ' ON og.optionid = go.id WHERE o.id = :id AND o.uniacid = :uniacid AND o.openid = :openid',
                array(
                    ':id' => $this->getOrderId(),
                    ':uniacid' => $this->getUniacid(),
                    ':openid' => $this->getOpenid()
                )
            );

            if ($order_goods_recharge['type'] == 11 || $order_goods_recharge['type'] == 12) {
                $order['mobile'] = $_GPC['telephone'];
                $order['title'] = $order_goods_recharge['title'];
            }
        }*/
        return show_json(1, array(
            'order' => $this->getOrder(),
            'set' => $this->getPaySet(),
            'credit' => $this->getCreditPay(),
            'wechat' => $this->getWechatPay(),
            'alipay' => $this->getAliPay(),
            'app_wechat' => $this->getAppWechatPay(),
            'app_alipay' => $this->getAppAliPay(),
            'unionpay' => $this->getUnionPay(),
            'yunpay' => $yunpay,
            'cash' => $this->getCash(),
            'storecash' => $this->getStoreCash(),
            'yeepay' => $this->getYeePay(),
            'gaohuitong' => $this->getGaohuitongPay(),
            'paypal' => $this->getPalPay(),
            'isweixin' => is_weixin(),
            'returnurl' => $this->getReturnUrl(),
            'goods' => $this->getOrderGoods()
        ));
    }
}
$class = new Display();
ddump($class->index());