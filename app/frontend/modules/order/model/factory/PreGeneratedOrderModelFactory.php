<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\order\model\factory;

use app\frontend\modules\order\model\OrderModel;

class PreCreateOrderModelFactory extends OrderModelFactory
{
    protected $source;
    public function getOrderModel(){
        $this->source = $this->getSourceByORM();
        return (new OrderModel($this->source));
    }
    function getSourceByORM(){

    }
}