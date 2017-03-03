<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 上午12:14
 */

namespace app\frontend\modules\order\model;


class WaitPayOrderModel extends OrderModel
{
    protected $create_time;
    protected $goods_total;
    protected $operation_models;//当前状态可操作的方法[接口名,id,名称]
    public function getStatusName(){

    }
    //对插件开放的方法
    public function setStatusName(){

    }
    //对插件开放的方法
    public function removeOperationModels(){

    }
    //对插件开放的方法
    public function addOperationModels(){

    }

}