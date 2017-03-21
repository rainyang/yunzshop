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
        $db_order = Order::find(80);
        $order_goods_list = $db_order->hasManyOrderGoods;
        foreach ($order_goods_list as $order_goods){
            $order_goods_models = new CreatedOrderGoodsModel($order_goods);
        }
        $order = new CreatedOrderModel($db_order,$order_goods_models);
        $order->update();
        return false;exit;
    }
}