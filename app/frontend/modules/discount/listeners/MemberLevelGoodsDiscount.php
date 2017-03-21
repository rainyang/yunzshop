<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\discount\listeners;

use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\discount\OrderGoodsDiscountWasCalculated;
use app\frontend\modules\order\controllers\PreGeneratedController;

class MemberLevelGoodsDiscount
{
    private $_event;
    public function needDiscount()
    {
        $this->_event->getMap();
        $OrderModel = $this->_event->getOrderModel();
        $OrderModel->getMemberModel();
        $OrderModel->getOrderGoodsModels();
        $OrderModel->getOrderGoodsModels()->getGoods();

        //商品设置了等级折扣
        //用户的等级在设置之内
        return true;
    }

    public function getDiscountDetails()
    {

        //member_model
        //goods_model
        $detail = [
            'name' => '会员等级折扣',
            'value' => '85',
            'price' => '50',
            'plugin' => '0',
        ];

        return $detail;
    }
    public function onDisplay(OnDiscountInfoDisplayEvent $even){
        $this->_event = $even;
        $order_model = $even->getOrderModel();
        $data = [
            'name' => '会员等级折扣',
            'value' => '85',
            'price' => '50',
            'plugin' => '1',
        ];
        $even->addMap('discount',$data);
    }
    public function handle(OrderGoodsDiscountWasCalculated $even)
    {

        if (!$this->needDiscount()) {
            return;
        }
        $even->addData($this->getDiscountDetails());

        return;
    }
    public function subscribe($events)
    {
        $events->listen(
            OnDiscountInfoDisplayEvent::class,
            MemberLevelGoodsDiscount::class.'@onDisplay'
        );
    }
}