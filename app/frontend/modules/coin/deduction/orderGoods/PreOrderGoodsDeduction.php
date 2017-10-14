<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\modules\coin\deduction\orderGoods;

use app\common\models\VirtualCoin;
use app\frontend\modules\coin\deduction\models\Deduction;
use app\frontend\modules\coin\deduction\orderGoods\amount\OrderGoodsDeductionAmount;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use \app\common\models\orderGoods\OrderGoodsDeduction;

/**
 * Class PreOrderGoodsDeduction
 * @package app\frontend\models\orderGoods
 * @property string code
 * @property string name
 * @property float usable_amount
 * @property float usable_coin
 * @property float used_amount
 * @property float used_coin
 */
class PreOrderGoodsDeduction extends OrderGoodsDeduction
{
    /**
     * @var PreOrderGoods
     */
    public $orderGoods;

    /**
     * @var \app\frontend\modules\coin\deduction\GoodsDeduction
     */
    public $goodsDeduction;
    /**
     * @var Deduction
     */
    private $deduction;
    private $usablePoint;
    private $orderDeduction;
    /**
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

        $orderGoods->setRelation($this->getCode() . 'OrderGoodsDeduction', $this);
    }

    private function setGoodsDeduction()
    {
        $goodsDeduction = app('DeductionManager')->make('GoodsDeductionManager')->make($this->getCode());
        /**
         * @var \app\frontend\modules\coin\deduction\GoodsDeduction $goodsDeduction
         */
        $this->goodsDeduction = $goodsDeduction->where('goods_id',$this->orderGoods->goods_id)->first();
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
    private function getOrderGoodsDeductionAmount(){
        return $this->orderGoodsDeductionAmount;
    }
    private function getGoodsDeduction(){
        return $this->goodsDeduction;
    }
    /**
     * 获取订单商品可用的爱心值
     * @return VirtualCoin
     */
    public function getUsableCoin()
    {
        if (isset($this->usablePoint)) {
            return $this->usablePoint;
        }

        if (!$this->getGoodsDeduction()) {
            // 购买商品不存在抵扣记录
            return $this->newCoin();
        }
        // 按比例
        $amount = $this->getOrderGoodsDeductionAmount()->getAmount();
//        // todo 按固定金额
//        // todo 抽象出抵扣价格类,按金额和按比例两个方法   金额,比例两个类 ,各有一个获取金额方法
        return $this->usablePoint = $this->newCoin()->setMoney($amount);
    }

    /**
     * @return VirtualCoin
     */
    private function getUsedCoin()
    {
        // 订单商品抵扣金额 * (订单商品集合抵扣金额/订单实际抵扣金额)
        $amount = $this->getUsableCoin()->getMoney() * ($this->getOrderDeduction()->getOrderGoodsCollectionDeduction()->getUsablePoint()->getMoney() / $this->getOrderDeduction()->getUsablePoint()->getMoney());
        return $this->newCoin()->setMoney($amount);
    }

    public function save(array $options = [])
    {
        $this->used_amount = $this->getUsedCoin()->getMoney();
        $this->used_coin = $this->getUsedCoin()->getCoin();
        return parent::save($options);
    }
}