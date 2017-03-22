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
    /**
     * 更新订单表
     * @return bool
     */
    protected function _updateTable(){
        return $this->order_model->save();
    }
    public function execute(){
        $DbOrder = Order::find(86);
        $order_goods_list = $DbOrder->hasManyOrderGoods;
        foreach ($order_goods_list as $_DbOrderGoods){
            $_OrderGoods = new CreatedOrderGoodsModel($_DbOrderGoods);
            //该订单商品价格
            $_OrderGoods->changePrice('190');
            $order_goods_models[] = $_OrderGoods;
        }
        $order = new CreatedOrderModel($DbOrder,$order_goods_models);
        //改订单价格
        $order->changePrice('380');
        //改运费
        $order->changeDispatchPrice('21');

        $order->update();
        exit;
        return false;
    }
    private function getOrderGoodsModels(){

    }
    private function changeOrderGoodsPrice(){

    }
}