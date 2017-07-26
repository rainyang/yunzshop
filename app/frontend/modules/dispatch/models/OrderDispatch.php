<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\dispatch\models;

use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\frontend\modules\order\models\PreGeneratedOrder;
use app\frontend\modules\order\services\OrderService;

class OrderDispatch
{
    private $preGeneratedOrder;

    public function __construct(PreGeneratedOrder $preGeneratedOrder)
    {
        $this->preGeneratedOrder = $preGeneratedOrder;
    }

    /**
     * 订单运费
     * @return float|int
     */
    public function getDispatchPrice()
    {
        if (false == OrderService::allGoodsIsReal($this->preGeneratedOrder->getOrderGoodsModels())) {
            return 0;
        }
        $event = new OrderDispatchWasCalculated($this->preGeneratedOrder);
        event($event);
        $data = $event->getData();
        return $result = array_sum(array_column($data, 'price'));
    }



    /**
     * 获取配送类型
     * @return mixed
     */
    public function getDispatchTypeId()
    {
        $dispatchTypeId = array_get(\YunShop::request()->get('address'), 'dispatch_type_id', 0);
        return $dispatchTypeId;
    }

}