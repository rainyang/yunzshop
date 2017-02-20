<?php
namespace mobile\order\pay;

class Base
{
    protected $openid;
    protected $uniacid;
    protected $orderid;
    protected $shopset;
    protected $set;

    public function __construct()
    {
        global $_W, $_GPC;
        require_once SZ_YI_PATH.'/site.php';
        $this->site = new \Sz_yiModuleSite();
        $this->openid = m("user")->getOpenid();
        $this->uniacid = $_W["uniacid"];
        $this->orderid = intval($_GPC["orderid"]);
        $this->payset = m('common')->getSysset(array('pay','shop'));
        load()->model('payment');
    }

    protected function getSite(){
        return $this->site;
    }

    protected function getOrderId()
    {
        return $this->orderid;
    }

    protected function getOpenid()
    {
        return $this->openid;
    }

    protected function getUniacid()
    {
        return $this->uniacid;
    }

    protected function getPaySet($key = null)
    {
        $payset = $this->payset;
        if ($payset) {
            if (isset($key)) {
                $data = explode('.', $key);
                foreach ($data as $v) {
                    $payset = $payset[$v];
                }
                return $payset;
            }
            return $payset;
        } else {
            return false;
        }
    }

    protected function isPost()
    {
        global $_W;
        return $_W['ispost'];
    }

    protected function getOrdersnGeneral()
    {
        $ordersn_general = pdo_fetchcolumn("SELECT ordersn_general FROM " . tablename('sz_yi_order') .
            ' WHERE id=:id AND uniacid = :uniacid AND openid = :openid limit 1',
            array(
                ':id' => $this->getOrderId(),
                ':uniacid' => $this->getUniacid(),
                ':openid' => $this->getOpenid()
            )
        );
        return $ordersn_general;
    }

    protected function getOrder($key = null)
    {
        $orderid = $this->getOrderId();
        if (!empty($orderid)) {
            $order_all = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_order') .
                ' WHERE ordersn_general = :ordersn_general AND uniacid = :uniacid AND openid = :openid',
                array(
                    ':ordersn_general' => $this->getOrdersnGeneral(),
                    ':uniacid' => $this->getUniacid(),
                    ':openid' => $this->getOpenid()
                )
            );
            if (empty($orderid)) {
                return show_json(0, '参数错误!');
            }
            //合并订单号订单大于1个，执行合并付款
            if (count($order_all) > 1) {
                $order = array();
                $order['ordersn'] = $this->getOrdersnGeneral();
                $orderid = array();
                foreach ($order_all as $key => $val) {
                    $order['price'] += $val['price'];
                    $order['deductcredit2'] += $val['deductcredit2'];
                    $order['ordersn2'] += $val['ordersn2'];
                    $orderid[] = $val['id'];
                }
                $order['status'] = $val['status'];
                $order['cash'] = $val['cash'];
                $order['openid'] = $val['openid'];
                $order['pay_ordersn'] = $val['pay_ordersn'];
            } else {
                $order = $order_all[0];
            }
            if (isset($key)) {
                return $order[$key];
            }
            return $order;
        }
    }

    protected function verifyStock()
    {
        $order = $this->getOrder();
        if ($order['order_type'] == '4' && $this->isPost()) {
            $goodstotal = pdo_fetchcolumn('SELECT total FROM ' . tablename('sz_yi_order_goods') .
                ' WHERE uniacid = :uniacid AND orderid = :orderid',
                array(
                    ':uniacid' => $this->getUniacid(),
                    ':orderid' => $order['id']
                )
            );

            // 本期数据
            $shengyu_codes = pdo_fetchcolumn('SELECT shengyu_codes FROM ' . tablename('sz_yi_indiana_period') .
                ' WHERE uniacid = :uniacid AND period_num = :period_num ',
                array(
                    ':uniacid' => $this->getUniacid(),
                    ':period_num' => $order['period_num']
                )
            );
            if ($goodstotal > $shengyu_codes) {
                return show_json(0, '剩余人次不足!');
            }
        }
    }
}