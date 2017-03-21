<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午8:16
 */

namespace app\frontend\modules\order\services\behavior;


abstract class ChangeStatusOperation extends OrderOperation
{
    /**
     * @var改变后状态
     */
    protected $status_after_changed;
    /**
     * 更新订单表
     * @return bool
     */
    protected function _updateTable(){
        $this->order_model->status = $this->status_after_changed;
        return $this->order_model->save();
    }

    /**
     * 执行订单操作
     * @return mixed
     */
    public function execute()
    {
        $result = $this->_updateTable();
        $this->_fireEvent();
        return $result;
    }
}