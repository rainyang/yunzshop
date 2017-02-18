<?php
namespace \addoons\sz_yi\core\mobile\order\express;
require __DIR__.'/base.php';

class Display extends Base {

    //获取订单详情
    private function _getOrderInfo() {
        $order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $this->orderid, ':uniacid' => $this->uniacid, ':openid' => $this->openid));
        return $order;
    }

    //获取订单商品的详情
    private function _getGoodsInfo() {
        $goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids  from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(':uniacid' => $this->uniacid, ':orderid' => $this->orderid));
        return $goods;
    }

    public function run() {
            $order = $this->_getOrderInfo();
            if (empty($order)) {
                return show_json(0);
            }

            $pindiana = p('indiana');
            $indiana = array();
            if($pindiana && $_GPC['indiana']){
                $indiana = $pindiana->getorder($order['period_num']);
            }

            $goods = $this->_getGoodsInfo();
            $order['goodstotal'] = count($goods);

            $set = set_medias($this->shopset, 'logo');

            return show_json(1, array('order' => $order, 'goods' => $goods, 'set' => $set, 'indiana' => $indiana));
    }
}

$class = new Display();
$class->run();
