<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 上午10:24
 */

namespace app\frontend\modules\dispatch\services\models;


abstract class Dispatch
{
    protected $_dispatch_details = [];
    public function __construct($dispatch_details)
    {
        $this->_dispatch_details = $dispatch_details;
    }
    /**
     * 为订单商品提供 获取商品的运费信息
     * @return array
     */
    public function getDispatchDetails(){
        return $this->_dispatch_details;
    }
    /**
     * 添加运费信息
     * @param $dispatch_detail
     */
    public function addDispatchDetail($dispatch_detail){
        $this->_dispatch_details[] = $dispatch_detail;
    }
}