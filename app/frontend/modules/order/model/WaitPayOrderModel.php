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
    protected $orm_model;
    protected $create_time;
    protected $goods_total;
    protected $status_name = '';

    protected $operation_models;//当前状态可操作的方法[接口名,id,名称]
    public function __construct($db_order_model)
    {
        $this->orm_model = $db_order_model;
    }

    public function getStatusName(){
        return '待付款';
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
    public function __get($name)
    {
        if(isset($this->$name)){
            return $this->$name;
        }
        if(isset($this->orm_model->$name)){
            return $this->orm_model->$name;
        }
        return null;
    }
}