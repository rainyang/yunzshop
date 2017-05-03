<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\dispatch\models;

class OrderDispatch
{

    /**
     * 订单运费
     * @return float|int
     */
    public function getDispatchPrice()
    {

        if (empty($this->_dispatch_details)) {
            return 0;
        }
        return $result = array_sum(array_column($this->_dispatch_details, 'price'));
    }


    /**
     * 获取配送类型
     * @return mixed
     */
    public function getDispatchTypeId()
    {
        $dispatchTypeId = array_get(\YunShop::request()->get('address'),'dispatch_type_id',0);
        return $dispatchTypeId;
    }

}