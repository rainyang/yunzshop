<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\order\model\factory;

abstract class OrderModelFactory
{
    protected $source;
    abstract function getOrderModel();
    function getSourceByORM(){

    }
}