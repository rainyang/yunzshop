<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/21
 * Time: 上午9:48
 */

namespace app\frontend\modules\order\services\models;


use app\common\exceptions\AppException;
use app\frontend\modules\discount\services\models\OrderDiscount;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\order\models\Order;

abstract class OrderModel extends Order
{
    /**
     * @var array 未插入数据库的订单商品数组
     */
    protected $orderGoodsModels = [];

    /**
     * @var \app\frontend\modules\dispatch\services\models\OrderDispatch 运费类实例
     */
    protected $orderDispatch;
    /**
     * @var OrderDiscount 优惠类实例
     */
    protected $orderDiscount;

    abstract protected function setDispatch();

    abstract protected function setDiscount();

    abstract public function setOrderGoodsModels(array $orderGoodsModels);

    /**
     * 统计商品总数
     * @return int
     */
    protected function getGoodsTotal()
    {
        //累加所有商品数量
        $result = 0;
        foreach ($this->orderGoodsModels as $orderGoodsModel) {
            $result += $orderGoodsModel->getTotal();
        }
        return $result;
    }

    /**
     * 计算订单优惠金额
     * @return number
     */
    protected function getDiscountPrice()
    {
        return $this->orderDiscount->getDiscountPrice();
    }

    /**
     * 获取订单抵扣金额
     * @return number
     */
    protected function getDeductionPrice()
    {
        return $this->orderDiscount->getDeductionPrice();
    }

    /**
     * 计算订单运费
     * @return int|number
     */
    protected function getDispatchPrice()
    {
        return $this->orderDispatch->getDispatchPrice();
    }

    /**
     * 计算订单成交价格
     * @return int
     */
    protected function getPrice()
    {
        //订单最终价格 = 商品最终价格 - 订单优惠 - 订单抵扣 + 订单运费
        $result = $this->getVipPrice() - $this->getDiscountPrice() - $this->getDeductionPrice() + $this->getDispatchPrice();
        if($result < 0 ){
            throw new AppException('('.$result.')订单金额不能为负');
        }
        return $result;
    }

    /**
     * 统计订单商品小计金额
     * @return int
     */
    protected function getVipPrice()
    {
        $result = 0;
        foreach ($this->orderGoodsModels as $OrderGoodsModel) {
            /**
             * @var $OrderGoodsModel PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoodsModel->getVipPrice();
        }
        return $result;
    }

    /**
     * 统计订单商品成交金额
     * @return int
     */
    protected function getOrderGoodsPrice()
    {
        $result = 0;
        foreach ($this->orderGoodsModels as $OrderGoodsModel) {
            $result += $OrderGoodsModel->getPrice();
        }
        return $result;
    }

}