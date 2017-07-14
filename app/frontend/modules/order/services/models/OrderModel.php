<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 上午9:48
 */

namespace app\frontend\modules\order\services\models;


use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\models\Order;
use Illuminate\Support\Collection;

abstract class OrderModel extends Order
{
    /**
     * @var Collection 未插入数据库的订单商品数组
     */
    protected $orderGoods = [];

    abstract public function setOrderGoods(Collection $orderGoods);

    /**
     * 统计商品总数
     * @return int
     */
    protected function getGoodsTotal()
    {
        //累加所有商品数量
        $result = 0;
        foreach ($this->orderGoods as $orderGoodsModel) {
            $result += $orderGoodsModel->getTotal();
        }
        return $result;
    }


    /**
     * 计算订单成交价格
     * @return int
     * @throws AppException
     */
    protected function getPrice()
    {
        //订单最终价格 = 商品最终价格 - 订单优惠 - 订单抵扣 + 订单运费
        $result = $this->getFinalPrice() - $this->getDiscountPrice() - $this->getDeductionPrice() + $this->getDispatchPrice();
        if($result < 0 ){
            throw new AppException('('.$result.')订单金额不能为负');
        }
        return $result;
    }

    /**
     * 统计订单商品小计金额
     * @return int
     */
    protected function getFinalPrice()
    {
        $result = 0;
        foreach ($this->orderGoods as $OrderGoodsModel) {
            /**
             * @var $OrderGoodsModel PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoodsModel->getFinalPrice();
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
        foreach ($this->orderGoods as $OrderGoodsModel) {
            $result += $OrderGoodsModel->getPrice();
        }
        return $result;
    }

}