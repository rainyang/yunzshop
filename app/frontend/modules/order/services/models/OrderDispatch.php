<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\order\services\models;

use Illuminate\Support\Facades\Event;

class OrderDispatch
{
    private $_order_model;
    private $_dispatch_details = [];

    public function __construct(PreGeneratedOrderModel $order_model)
    {
        $this->_order_model = $order_model;
        Event::fire(new \app\common\events\OrderDispatchWasCalculated($this));
    }
    //(订单)获取配送类型
    public function getDispatchTypeId(){
        return \YunShop::request()->get('dispatch_type_id');
    }
    // 获取商品可选配送方式
    public function getDispatchTypes()
    {
        $data[] = [
            'id' => 1,
            'name' => '快递',
            'plugin' => 0
        ];
        return $data;
    }

    //提供给订单 累加所有监听者提供的运费
    public function getDispatchPrice()
    {
        return $result = array_sum(array_column($this->_dispatch_details, 'price'));
    }

    //提供给监听者 获取订单model
    public function getOrderModel()
    {
        return $this->_order_model;
    }

    //提供给监听者 添加一种运费
    public function addDispatchDetail($dispatch_detail)
    {
        $this->_dispatch_details[] = $dispatch_detail;
    }

    //提供给订单 返回运费详情
    public function getDispatchDetails()
    {
        return $this->_dispatch_details;

    }


}