<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\models\orderGoods;

use app\common\models\VirtualCoin;
use app\frontend\models\GoodsDeduction;
use app\frontend\models\OrderGoods;
use app\frontend\modules\coin\deduction\models\Deduction;

class PreOrderGoodsDeduction extends \app\common\models\orderGoods\OrderGoodsDeduction
{
    /**
     * @var OrderGoods
     */
    public $orderGoods;

    /**
     * @var GoodsDeduction
     */
    public $goodsDeduction;
    /**
     * @var Deduction
     */
    private $deduction;
    private $usablePoint;
    private $orderDeduction;

    public function __construct(array $attributes = [], $orderGoods, $orderDeduction, $deduction)
    {
        parent::__construct($attributes);

        $this->setDeduction($deduction);

        $this->setOrderDeduction($orderDeduction);

        $this->setOrderGoods($orderGoods);

        $this->setGoodsDeduction();

        $this->code = $this->getCode();
        $this->name = $this->getName();

        $this->usable_amount = $this->getUsablePoint()->getMoney();
        $this->usable_coin = $this->getUsablePoint()->getCoin();

    }

    private function getCode()
    {
        return $this->getDeduction()->getCode();
    }

    /**
     * @return Deduction
     */
    private function getDeduction()
    {

        return $this->deduction;
    }

    private function getName()
    {
        return $this->getDeduction()->getName();
    }

    /**
     * @return VirtualCoin
     */
    private function newCoin()
    {
        return app('CoinManager')->make($this->getCode());
    }

    private function setOrderGoods($orderGoods)
    {
        $this->orderGoods = $orderGoods;
        $this->uid = $orderGoods->uid;
        // todo 优化命名
        $orderGoods->setRelation($this->getCode() . 'OrderGoodsDeduction', $this);
    }

    private function setGoodsDeduction()
    {
        $goodsDeduction = app('DeductionManager')->make('GoodsDeductionManager')->make($this->getCode());
        $this->goodsDeduction = $goodsDeduction->whereGoodsId($this->orderGoods->goods_id)->first();

    }

    private function setOrderDeduction($orderDeduction)
    {
        $this->orderDeduction = $orderDeduction;
    }

    private function setDeduction($deduction)
    {
        $this->deduction = $deduction;
    }

    /**
     * 订单抵扣模型
     * @return mixed
     * @throws \Exception
     */
    private function getOrderDeduction()
    {
        if (!isset($this->orderDeduction)) {
            throw new \Exception('未设置OrderDeduction');
        }
        return $this->orderDeduction;
    }

    /**
     * 获取订单商品可用的爱心值
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {
        if (isset($this->usablePoint)) {
            return $this->usablePoint;
        }

        if (!isset($this->goodsDeduction)) {
            // 购买商品不存在抵扣记录
            return $this->newCoin();
        }

        $virtualCoin = $this->goodsDeduction->getUsableLoveCoin($this->orderGoods->goods_price);
        return $this->usablePoint = $virtualCoin;
    }


    /**
     * @return VirtualCoin
     */
    private function getUsedPoint()
    {
        // 订单商品抵扣金额 * (订单商品集合抵扣金额/订单实际抵扣金额)
        $amount = $this->getUsablePoint()->getMoney() * ($this->getOrderDeduction()->getOrderGoodsCollectionDeduction()->getUsablePoint()->getMoney() / $this->getOrderDeduction()->getUsablePoint()->getMoney());
        return $this->newCoin()->setMoney($amount);
    }

    public function save(array $options = [])
    {
        $this->used_amount = $this->getUsedPoint()->getMoney();
        $this->used_coin = $this->getUsedPoint()->getCoin();
        return parent::save($options);
    }
}