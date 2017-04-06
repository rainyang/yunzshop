<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\dispatch\services\models;

class OrderDispatch extends Dispatch
{

    //todo 获取商品可选配送方式
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

        if (empty($this->_dispatch_details)) {
            return 0;
        }
        return $result = array_sum(array_column($this->_dispatch_details, 'price'));
    }


    //todo 获取配送类型
    public function getDispatchTypeId()
    {
        $dispatchTypeId = array_get(\YunShop::request()->get('address'),'dispatch_type_id',0);
        return $dispatchTypeId;
    }

}