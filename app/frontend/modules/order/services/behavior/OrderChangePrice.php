<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午5:03
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;
use app\frontend\modules\goods\services\models\CreatedOrderGoodsModel;
use app\frontend\modules\order\services\models\CreatedOrderModel;

class OrderChangePrice extends OrderOperation
{
    protected $status_before_change = [ORDER::WAIT_PAY];
    protected $status_after_changed = ORDER::WAIT_PAY;
    protected $name = '改价';
    protected $past_tense_class_name = 'OrderChangedPrice';
    protected $_orderGoodsModels = [];

    /**
     * 更新订单表
     * @return bool
     */
    protected function updateTable()
    {
        return $this->order->save();
    }

    public function execute()
    {
        $this->addChangeOrderGoodsPriceInfo();

        $order = new CreatedOrderModel($this->order, $this->getOrderGoodsModels());
        //改订单价格 todo 测试
        //$order->addChangePriceInfo('380');
        //改运费 todo 测试
        $order->addChangeDispatchPriceInfo('21');

        $order->update();

        return false;
    }

    private function getOrderGoodsModels()
    {
        if (count($this->_orderGoodsModels)) {
            return $this->_orderGoodsModels;
        }
        $order_goods_list = $this->order->hasManyOrderGoods;
        foreach ($order_goods_list as $_DbOrderGoods) {
            $_OrderGoods = new CreatedOrderGoodsModel($_DbOrderGoods);
            $this->_orderGoodsModels[] = $_OrderGoods;
        }
        return $this->_orderGoodsModels;
    }

    private function addChangeOrderGoodsPriceInfo()
    {
        foreach ($this->getOrderGoodsModels() as $orderGoodsModel) {
            //该订单商品价格 todo 测试
            $orderGoodsModel->addChangePriceInfo('190');
        }
    }
}