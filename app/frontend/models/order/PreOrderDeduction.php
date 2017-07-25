<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;


use app\frontend\modules\order\models\PreGeneratedOrder;

class PreOrderDeduction extends \app\common\models\order\OrderDeduction
{
    public $order;
    public $checked = false;
    public function setOrder(PreGeneratedOrder $order)
    {
        $this->order = $order;
        $order->orderDeductions->push($this);
    }
    public function save(array $options = [])
    {

        if(!$this->checked){
            return false;
        }
        return parent::save($options);
    }
}