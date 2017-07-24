<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;

class WaitSend extends Status
{
    private $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getStatusName()
    {
        return '待发货';
    }

    public function getButtonModels()
    {
        $result =
            [

            ];
        //$result = array_merge($result,self::getRefundButtons($this->order));

        return $result;
    }
}