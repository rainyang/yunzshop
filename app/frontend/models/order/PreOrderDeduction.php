<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;

use app\common\models\order\OrderDeduction;
use app\common\models\VirtualCoin;
use app\frontend\models\MemberCoin;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\deduction\OrderGoodsDeductionCollection;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

/**
 * Class PreOrderDeduction
 * @package app\frontend\models\order
 * @property int uid
 * @property int coin
 * @property int amount
 * @property int name
 * @property int code
 */
class PreOrderDeduction extends OrderDeduction
{
    public function toArray()
    {
        $this->amount = sprintf('%.2f', $this->amount);

        $this->coin = sprintf('%.2f', $this->coin);
        return parent::toArray();
    }

    protected $appends = ['checked'];
    /**
     * @var PreOrder
     */
    public $order;
    private $deduction;
    /**
     * @var MemberCoin
     */
    private $memberCoin;
    private $virtualCoin;
    /**
     * @var OrderGoodsDeductionCollection
     */
    private $orderGoodsDeductionCollection;
    /**
     * @var VirtualCoin
     */
    private $useablePoint;

    public function __construct(array $attributes = [], $deduction, $order, $virtualCoin)
    {
        $this->deduction = $deduction;
        $this->virtualCoin = $virtualCoin;

        $this->setOrder($order);
        $this->setOrderGoodsDeductions();

        $this->_init();
        parent::__construct($attributes);
    }

    private function setOrder(PreOrder $order)
    {
        $this->order = $order;
    }

    private function deductible()
    {
        return $this->getUsablePoint()->getCoin() > 0;
    }

    /**
     * 实例化并绑定所有的订单商品抵扣实例,集合  并将集合绑定在订单抵扣上
     */
    private function setOrderGoodsDeductions()
    {
        $orderGoodsDeductionCollection = $this->order->orderGoods->map(function (PreOrderGoods $aOrderGoods) {
            return new PreOrderGoodsDeduction([], $aOrderGoods, $this, $this->getDeduction());
        });
        $this->orderGoodsDeductionCollection = new  OrderGoodsDeductionCollection($orderGoodsDeductionCollection);
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

        return app('CoinManager')->make('MemberCoinManager')->make($code, $this->order->getMember());
    }

    private function _init()
    {
        $this->uid = $this->order->uid;


        $this->coin = $this->getUsablePoint()->getCoin();
        $this->amount = $this->getUsablePoint()->getMoney();
        $this->code = $this->getCode();
        $this->name = $this->getName();
        if ($this->deductible()) {
            $this->order->orderDeductions->push($this);
        }
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
        if (isset($this->useablePoint)) {
            return $this->useablePoint;
        }
        $result = $this->newCoin();

        // 购买者不存在虚拟币记录
        if (!$this->getMemberCoin()) {

            return $this->useablePoint = $result;
        }

        // 累加所有订单商品的可用虚拟币
        /**
         * @var VirtualCoin $virtualCoin
         */

        $orderGoodsVirtualCoin = $this->getOrderGoodsDeductionCollection()->getUsablePoint();

        // 商品可抵扣虚拟币+运费可抵扣虚拟币
        $orderVirtualCoin = $orderGoodsVirtualCoin->plus($this->getDispatchPriceDeductionPoint());

        // 不能超过订单使用其他抵扣金额后的价格
        //$afterOtherDeductionAmount = min($this->order->price - $this->getOtherDeducitonAmount(),$orderVirtualCoin->getMoney());
        // 订单商品价格计算，不包含运算
        $afterOtherDeductionAmount = min($this->order->goods_price - $this->getOtherDeducitonAmount(),$orderVirtualCoin->getMoney());

        // 取(用户可用虚拟币)与(订单抵扣虚拟币)的最小值
        $amount = min($this->getMemberCoin()->getMaxUsableCoin()->getMoney(), $afterOtherDeductionAmount);

        return $this->useablePoint = $this->newCoin()->setMoney($amount);
    }
    private function getOtherDeducitonAmount()
    {
        return $this->order->orderDeductions->sum(function ($orderDeduction) {
            if($orderDeduction->isChecked()){
                return $orderDeduction->getUsablePoint()->getMoney();
            }
            return 0;
        });
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
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductionCollection()
    {
        return $this->orderGoodsDeductionCollection;

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

    public function getCheckedAttribute()
    {
        return $this->isChecked();
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        $deduction_codes = $this->order->getParams('deduction_ids');

        if (!is_array($deduction_codes)) {
            $deduction_codes = json_decode($deduction_codes, true);
            if (!is_array($deduction_codes)) {
                $deduction_codes = explode(',', $deduction_codes);
            }
        }

        return in_array($this->getCode(), $deduction_codes);
    }

    public function save(array $options = [])
    {
        if (!$this->isChecked() || $this->getOrderGoodsDeductionCollection()->getUsablePoint() <= 0) {
            // todo 应该返回什么
            return true;
        }
        $this->getMemberCoin()->consume($this->getUsablePoint(), ['order_sn' => $this->order->order_sn]);

        return parent::save($options);
    }
}