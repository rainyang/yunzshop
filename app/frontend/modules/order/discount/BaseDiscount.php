<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;

use app\frontend\modules\order\models\PreOrder;

abstract class BaseDiscount
{
    /**
     * @var PreOrder
     */
    protected $order;
    public function __construct(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * 获取金额
     * @return int
     */
    abstract public function getAmount();

}