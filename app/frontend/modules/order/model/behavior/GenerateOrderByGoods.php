<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午12:00
 */

namespace app\frontend\modules\order\model\behavior;
use app\common\models\Goods;
use app\common\models\Order;
use app\frontend\modules\member\model\MemberModel;
use app\frontend\modules\order\model\PreGeneratedOrderModel;
use app\frontend\modules\order\service\OrderService;
use app\frontend\modules\shop\model\ShopModel;


class GenerateByGoods
{
    private $shop_model;
    private $member_model;

    public function __construct(PreGeneratedOrderModel $order_model,MemberModel $member_model,ShopModel $shop_model)
    {
        $this->order_model = $order_model;
    }

    public function setMemberModel(MemberModel $member_model){
        $this->member_model = $member_model;

    }
    public function setShopModel(ShopModel $shop_model){
        $this->shop_model = $shop_model;

    }
    public function create(){
        $this->createOrder();
        $this->createOrderGoods();

    }
    private function createOrder(){
        $data = array(
            'shop_id'=>$this->shop_model->getShopId(),
            'member_id'=>$this->member_model->getMemberId(),
            'order_sn'=>OrderService::createOrderSn(),
            'order_price'=>$this->order_model->getPrice(),
            'goods_price'=>$this->order_model->getGoodsPrice(),
            'create_time'=>time(),
        );
        return Order::insertGetId($data);
    }

}