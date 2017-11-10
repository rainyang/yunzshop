<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:52
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;
use Yunshop\StoreCashier\common\models\Store;

class StatusFactory
{
    /**
     * @var Order
     */
    private $order;

    function __construct($order)
    {
        $this->order = $order;
    }

    public function create()
    {
        $order = $this->order;
        switch ($order->status) {
            case -1:
                return new Close($order);
                break;
            case 0:
                return $this->waitPay();
                break;
            case 1:
                return new WaitSend($order);
                break;
            case 2:
                return $this->waitReceive();
                break;
            case 3:
                return new Complete($order);
                break;

        }
    }
    private function waitPay()
    {


        if (app('plugins')->isEnabled('store-cashier') && $this->order->plugin_id == Store::PLUGIN_ID){
            //门店订单
            return (new \Yunshop\StoreCashier\common\order\status\WaitPay())->handle($this->order);

        } else {
            // 正常订单
            return new WaitPay($this->order);
        }

    }
    private function waitReceive()
    {


        if (app('plugins')->isEnabled('store-cashier') && $this->order->plugin_id == Store::PLUGIN_ID){
            //门店订单
            return (new \Yunshop\StoreCashier\common\order\status\WaitReceive())->handle($this->order);

        } else {
            // 正常订单
            return new WaitReceive($this->order);
        }

    }
}