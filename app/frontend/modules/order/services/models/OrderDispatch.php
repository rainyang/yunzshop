<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\order\services\models;

use app\common\events\OrderPriceWasCalculated;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderDispatch implements ShouldQueue
{
    private $_order_model;

    //todo 待实现
    public function getDispatchPrice()
    {

        $result = 0;
        dd($this->_order_model->getOrderGoodsModels());
        foreach ($this->_order_model->getOrderGoodsModels() as $order_goods){
            dd($order_goods->getDispatchPrice());

            $result += $order_goods->getDispatchPrice();
        }
        return $result;
    }

    public function handle(OrderPriceWasCalculated $even)
    {
        $this->_order_model = $even->getOrderModel();
        $this->_order_model->setDispatchPrice($this->getDispatchPrice());
        dd($this->_order_model);
        return;
    }
}