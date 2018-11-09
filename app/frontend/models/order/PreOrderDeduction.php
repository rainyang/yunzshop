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
    /**
     * @return array
     */
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

    /**
     * PreOrderDeduction constructor.
     * @param array $attributes
     * @param $deduction
     * @param $order
     * @param $virtualCoin
     */
    public function __construct(array $attributes = [], $deduction, $order, $virtualCoin)
    {
        $this->deduction = $deduction;
        $this->virtualCoin = $virtualCoin;

        $this->setOrder($order);
        $this->setOrderGoodsDeductions();

        $this->_init();
        parent::__construct($attributes);
    }

    /**
     * @param PreOrder $order
     */
    private function setOrder(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * 下单时此抵扣可选
     * @return bool
     */
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
     * 下单用户此抵扣对应虚拟币的余额
     * @return MemberCoin
     */
    private function getMemberCoin()
    {
        if (isset($this->memberCoin)) {
            return $this->memberCoin;
        }
        $code = $this->getCode();

        return app('CoinManager')->make('MemberCoinManager')->make($code, $this->order->belongsToMember);
    }

    /**
     *
     */
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
     * 此抵扣对应的虚拟币
     * @return VirtualCoin
     */
    private function newCoin()
    {
        return app('CoinManager')->make($this->getCode());
    }

    /**
     * 订单中实际可用的此抵扣
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {
        if (isset($this->useablePoint)) {
            return $this->useablePoint;
        }
        debug_log()->deduction('开始订单抵扣',"{$this->getName()} 计算可用金额");

        $result = $this->newCoin();

        // 购买者不存在虚拟币记录
        if (!$this->getMemberCoin()) {
            debug_log()->deduction('订单抵扣',"{$this->getName()} 用户没有对应虚拟币");

            return $this->useablePoint = $result;
        }




        // 商品金额抵扣 不能超过订单除去运费后 使用其他抵扣金额后的价格
        $deductionAmount = min($this->order->price - $this->order->getDispatchAmount() - $this->getOtherDeductionAmount(), $this->getMaxDeduction()->getMoney());
        // 抵扣金额 = 商品抵扣金额 + 运费抵扣金额
        $deductionAmount += $this->getMaxDispatchPriceDeduction()->getMoney();

        // 取(用户可用虚拟币)与(订单抵扣虚拟币)的最小值
        $amount = min($this->getMemberCoin()->getMaxUsableCoin()->getMoney(), $deductionAmount);

        return $this->useablePoint = $this->newCoin()->setMoney($amount);
    }

    /**
     * 订单中已经参与了计算的其他抵扣总金额
     * todo 修改订单中的获取抵扣金额方法,然后删除这个方法
     * @return mixed
     */
    private function getOtherDeductionAmount()
    {
        debug_log()->deduction('订单抵扣',"{$this->getName()} 计算其他抵扣金额");

        return $this->order->orderDeductions->sum(function (PreOrderDeduction $orderDeduction) {
            if ($orderDeduction->isChecked()) {
                return $orderDeduction->getUsablePoint()->getMoney();
            }
            return 0;
        });
    }

    /**
     * 获取订单商品占用的抵扣金额
     * @return float|int
     */
    public function getOrderGoodsDeductionAmount()
    {

        $amount = ($this->getMaxOrderGoodsDeduction()->getMoney() / $this->getMaxDeduction()->getMoney()) * $this->getUsablePoint()->getMoney();
        return $amount;
    }

    /**
     * 订单中此抵扣可用最大值
     * @return VirtualCoin
     */
    private function getMaxDeduction()
    {
        debug_log()->deduction('订单抵扣',"{$this->getName()} 计算最大抵扣");

        $result =  $this->getMaxOrderGoodsDeduction();

        return $result;

    }

    /**
     * 最多可抵扣商品金额的虚拟币
     * 累加所有订单商品的可用虚拟币
     * @return VirtualCoin
     */
    public function getMaxOrderGoodsDeduction()
    {
        return $this->getOrderGoodsDeductionCollection()->getUsablePoint();
    }

    /**
     * 最多可抵扣运费的虚拟币
     * @return VirtualCoin
     */
    private function getMaxDispatchPriceDeduction()
    {
        $result = $this->newCoin();

        //开关
        if ($this->getDeduction()->isEnableDeductDispatchPrice()) {

            //订单运费
            $amount = $this->order->getDispatchAmount();

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

    /**
     * @return bool
     */
    public function getCheckedAttribute()
    {
        return $this->isChecked();
    }

    /**
     * 选择了此抵扣
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

    public function beforeSaving()
    {
        if (!$this->isChecked() || $this->getOrderGoodsDeductionCollection()->getUsablePoint() <= 0) {
            // todo 应该返回什么
            return false;
        }
        $this->getMemberCoin()->consume($this->getUsablePoint(), ['order_sn' => $this->order->order_sn]);

        return parent::beforeSaving(); // TODO: Change the autogenerated stub
    }

}