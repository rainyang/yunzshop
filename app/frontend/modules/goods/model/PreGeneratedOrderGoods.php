<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\model;


use app\common\models\Goods;
use app\frontend\modules\order\model\OrderModel;

class OrderGoods
{
    private $order_model;
    private $goods_model;

    public function __construct(OrderModel $order_model,GoodsModel $goods_model)
    {
        $this->order_model = $order_model;
        $this->goods_model = $goods_model;

    }

    public function saveToDB()
    {
        $this->order_model->getOrderId();
        $data = array(
            'shop_id' => $this->order_model->getShopModel()->getShopId(),
            'order_id' => $this->order_model->getOrderId(),
            'goods_id' => $this->goods_model->getGoodsId(),
            'goods_sn' => $this->goods_model->getGoodsSn(),
            'member_id' => $this->order_model->getMemberModel()->getMemberId(),
            'price' => $this->getPrice()
        );
        Goods::insertGetId($data);
    }
}