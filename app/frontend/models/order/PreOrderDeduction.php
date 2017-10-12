<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;

use app\common\models\VirtualCoin;
use app\frontend\models\MemberCoin;
use app\frontend\models\orderGoods\PreOrderGoodsDeduction;
use app\frontend\modules\coin\deduction\models\Deduction;
use app\frontend\modules\coin\deduction\models\OrderGoodsCollectionDeduction;
use app\frontend\modules\order\models\PreOrder;

/**
 * Class PreOrderDeduction
 * @package app\frontend\models\order
 * @property int uid
 */
class PreOrderDeduction extends \app\common\models\order\OrderDeduction
{
    /**
     * @var PreOrder
     */
    public $order;
    private $orderGoodsCollectionDeduction;
    private $deduction;
    /**
     * @var MemberCoin
     */
    private $memberCoin;
    private $coin;

    public function __construct(array $attributes = [], $deduction, $order, $coin)
    {
        $this->setDeduction($deduction);
        $this->setCoin($coin);
        $this->setOrder($order);
        $this->setOrderGoodsDeductions();
        $this->_init();
        parent::__construct($attributes);
    }

    private function setDeduction($deduction)
    {
        $this->deduction = $deduction;
    }

    private function setOrder(PreOrder $order)
    {
        $this->order = $order;
    }

    private function setOrderGoodsDeductions()
    {
        $orderGoodsDeductions = $this->order->orderGoods->map(function ($aOrderGoods) {
            return new PreOrderGoodsDeduction([], $aOrderGoods, $this, $this->getDeduction());
        });
        $this->setRelation('orderGoodsDeductions', $orderGoodsDeductions);
    }

    private function setCoin($coin)
    {
        $this->coin = $coin;
    }

    /**
     * @return MemberCoin
     */
    private function getMemberCoin()
    {
        if (isset($this->memberCoin)) {
            return $this->memberCoin;
        }
        $code = $this->getCode();
        $memberCoin = app('CoinManager')->make('MemberCoinManager')->make($code);
        return $this->memberCoin = $memberCoin->whereMemberId($this->uid)->first();
    }

    private function _init()
    {
        $this->uid = $this->order->uid;
        $this->order->orderDeductions->push($this);
        $this->coin = $this->getUsablePoint()->getCoin();
        $this->amount = $this->getUsablePoint()->getMoney();
        $this->code = $this->getCode();
        $this->name = $this->getName();

    }

    /**
     * @return Deduction
     */
    private function getDeduction()
    {
        return $this->deduction;
    }

    /**
     * @return VirtualCoin
     */
    private function newCoin()
    {
        return app('CoinManager')->make($this->getCode());
    }

    /**
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {
        $result = $this->newCoin();

        // 购买者不存在华侨币记录
        if (!$this->getMemberCoin()) {
            return $result;
        }

        // 累加所有订单商品的可用华侨币
        /**
         * @var VirtualCoin $virtualCoin
         */
        $virtualCoin = $this->getOrderGoodsCollectionDeduction()->getUsablePoint();

        // 商品可抵扣爱心值+运费可抵扣爱心值
        $virtualCoin->plus($this->getDispatchPriceDeductionPoint());

        // 取(用户可用爱心值)与(订单抵扣爱心值)的最小值
        $amount = min($this->getMemberCoin()->getMaxUsableLovePoint(), $virtualCoin->getMoney());

        return $this->newCoin()->setMoney($amount);
    }

    /**
     * 抵扣运费的爱心值
     * @return VirtualCoin
     */
    public function getDispatchPriceDeductionPoint()
    {
        $result = $this->newCoin();

        //开关
        if ($this->getDeduction()->isEnableDeductDispatchPrice()) {
            //订单运费
            $amount = $this->order->getDispatchPrice();

            $result->setMoney($amount);
        }

        return $result;
    }

    /**
     * @return OrderGoodsCollectionDeduction
     */
    public function getOrderGoodsCollectionDeduction()
    {
        if (isset($this->orderGoodsCollectionDeduction)) {
            return $this->orderGoodsCollectionDeduction;
        }
        return $this->orderGoodsCollectionDeduction = new OrderGoodsCollectionDeduction($this->orderGoodsDeductions);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getDeduction()->getCode();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getDeduction()->getName();
    }

    /**
     * @return int
     */
    public function getDeductionId()
    {
        $this->getDeduction()->getId();
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        $this->getDeduction()->isEnable();
    }


    /**
     * @return bool
     */
    public function isChecked()
    {
        $deduction_ids = $this->order->getParams('deduction_ids');

        if (!is_array($deduction_ids)) {
            $deduction_ids = json_decode($deduction_ids, true);
            if (!is_array($deduction_ids)) {
                $deduction_ids = explode(',', $deduction_ids);
            }
        }
        return in_array($this->getDeductionId(), $deduction_ids);

    }

    public function save(array $options = [])
    {

        if (!$this->isChecked()) {
            // todo 应该返回什么
            return true;
        }
        return parent::save($options); // TODO: Change the autogenerated stub
    }
}