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

class MemberLevelGoodsDiscount
{
    private $_event;
    public function needDiscount()
    {


        //商品设置了等级折扣
        //用户的等级在设置之内
        return true;
    }

    public function getDiscountDetails()
    {

        $detail = [
            'name' => '会员等级折扣',
            'value' => '85',
            'price' => '-50',
            'plugin' => '0',
        ];

        return $detail;
    }
    public function onDisplay(OnDiscountInfoDisplayEvent $event){
        $this->_event = $event;

        $order_model = $event->getOrderModel();
        $data[] = [
            'name' => '会员等级折扣',
            'value' => '85',
            'price' => '50',
            'plugin' => '1',
        ];
        $event->addMap('discount',$data);
    }
    public function handle(OrderGoodsDiscountWasCalculated $event)
    {
        $this->_event = $event;
        if (!$this->needDiscount()) {
            return;
        }
        $event->addData($this->getDiscountDetails());

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