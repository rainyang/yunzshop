<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\modules\deduction\orderGoods;

use app\common\models\VirtualCoin;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\deduction\orderGoods\amount\FixedAmount;
use app\frontend\modules\deduction\orderGoods\amount\GoodsPriceProportion;
use app\frontend\modules\deduction\orderGoods\amount\Invalid;
use app\frontend\modules\deduction\orderGoods\amount\OrderGoodsDeductionAmount;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use \app\common\models\orderGoods\OrderGoodsDeduction;

/**
 * 订单商品抵扣
 * Class PreOrderGoodsDeduction
 * @package app\frontend\models\orderGoods
 * @property string code
 * @property string name
 * @property float usable_amount
 * @property float usable_coin
 * @property float used_amount
 * @property float used_coin
 * @property int uid
 */
class PreOrderGoodsDeduction extends OrderGoodsDeduction
{
    /**
     * @var PreOrderGoods
     */
    public $orderGoods;

    /**
     * @var \app\frontend\modules\deduction\GoodsDeduction
     */
    public $goodsDeduction;
    /**
     * 抵扣模型
     * @var Deduction
     */
    private $deduction;
    /**
     * 可用的虚拟币
     * @var VirtualCoin
     */
    private $usablePoint;
    /**
     * 订单抵扣模型
     * @var PreOrderDeduction
     */
    private $orderDeduction;
    /**
     * 订单商品金额类
     * @var OrderGoodsDeductionAmount
     */
    private $orderGoodsDeductionAmount;

    public function __construct(array $attributes = [], $orderGoods, $orderDeduction, $deduction)
    {
        parent::__construct($attributes);

        $this->deduction = $deduction;
        $this->orderDeduction = $orderDeduction;

        $this->setOrderGoods($orderGoods);

        $this->setGoodsDeduction();

        $this->code = $this->getCode();
        $this->name = $this->getName();

        $this->usable_amount = $this->getUsableCoin()->getMoney();
        $this->usable_coin = $this->getUsableCoin()->getCoin();
        echo '<pre>';print_r($this->usable_coin);exit();

    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
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

    private function setOrderGoods(PreOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
        $this->uid = $orderGoods->uid;
        $this->orderGoods->orderGoodsDeductions->push($this);
    }

    private function setGoodsDeduction()
    {
        $this->goodsDeduction = app('DeductionManager')->make('GoodsDeductionManager')->make($this->getCode(), $this->orderGoods->goods);

    }

    /**
     * 订单抵扣模型
     * @return PreOrderDeduction
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
     * @return OrderGoodsDeductionAmount
     */
    private function getOrderGoodsDeductionAmount()
    {
        // 从商品抵扣中获取到类型
        switch ($this->getGoodsDeduction()->getDeductionAmountCalculationType()) {
            case 'FixedAmount':
                $this->orderGoodsDeductionAmount = new FixedAmount($this->orderGoods, $this->getGoodsDeduction());
                break;
            case 'GoodsPriceProportion':
                $this->orderGoodsDeductionAmount = new GoodsPriceProportion($this->orderGoods, $this->getGoodsDeduction());
                break;
            default:
                $this->orderGoodsDeductionAmount = new Invalid($this->orderGoods, $this->getGoodsDeduction());
                break;
        }
        return $this->orderGoodsDeductionAmount;
    }

    private function getGoodsDeduction()
    {
        return $this->goodsDeduction;
    }

    /**
     * 获取订单商品可用的虚拟币
     * @return VirtualCoin
     */
    public function getUsableCoin()
    {
        if (isset($this->usablePoint)) {
            return $this->usablePoint;
        }

        return $this->usablePoint = $this->_getUsableCoin();
    }

    private function _getUsableCoin()
    {
        if (!$this->getGoodsDeduction() || !$this->getGoodsDeduction()->deductible($this->orderGoods->goods)) {
            // 购买商品不存在抵扣记录
            return $this->newCoin();
        }

        $amount = $this->getOrderGoodsDeductionAmount()->getAmount();

        $coin =   $this->newCoin()->setMoney($amount);
        return $coin;
    }

    /**
     * @return VirtualCoin
     */
    public function getUsedCoin()
    {
        // 订单商品 积分抵扣 金额 * (订单商品集合 积分抵扣 金额/订单实际使用 积分抵扣 金额)
        if(!$this->orderDeduction->isChecked()){
            return $this->newCoin();
        }


        $amount = $this->getUsableCoin()->getMoney() * ($this->getOrderDeduction()->getUsablePoint()->getMoney()/$this->getOrderDeduction()->getOrderGoodsDeductionCollection()->getUsablePoint()->getMoney());

        return $this->newCoin()->setMoney($amount);
    }

    public function used()
    {
        return $this->orderDeduction->isChecked() && $this->getUsedCoin()->getCoin() > 0;
    }

    public function save(array $options = [])
    {
        if (!$this->used()) {
            return true;
        }

        $this->used_amount = $this->getUsedCoin()->getMoney();
        $this->used_coin = $this->getUsedCoin()->getCoin();
        return parent::save($options);
    }
}