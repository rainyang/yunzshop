<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/9
 * Time: 10:50 AM
 */

namespace app\frontend\modules\deduction;


use app\common\modules\orderGoods\models\PreOrderGoods;
use app\framework\Database\Eloquent\Collection;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\order\models\PreOrder;

class OrderDeductManager
{
    /**
     * @var PreOrder
     */
    private $order;
    /**
     * @var float
     */
    private $amount;
    /**
     * @var OrderDeductionCollection
     */
    private $orderDeductionCollection;
    /**
     * @var OrderDeductionCollection
     */
    private $checkedOrderDeductionCollection;
    /**
     * @var OrderGoodsDeductionCollection
     */
    private $orderGoodsDeductionCollection;
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $deductions;

    public function __construct(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * @return OrderDeductionCollection
     */
    public function getOrderDeductions()
    {
        if (!isset($this->orderDeductionCollection)) {
            $this->orderDeductionCollection = $this->getAllOrderDeductions();

            // 过滤调不能抵扣的项
            $this->orderDeductionCollection->filterNotDeductible();
            // 验证
            $this->orderDeductionCollection->validate();
            // 按照选中状态排序
            $this->orderDeductionCollection->sortOrderDeductionCollection();

            $this->order->setRelation('orderDeductions', $this->orderDeductionCollection);
        }
        return $this->orderDeductionCollection;
    }

    /**
     * 获取并订单抵扣项并载入到订单模型中
     * @return OrderDeductionCollection
     */
    public function getAllOrderDeductions()
    {
        $orderDeductions = $this->getEnableDeductions()->map(function (Deduction $deduction) {

            $orderGoodsDeductionCollection = $this->getOrderGoodsDeductionCollection()->where('code', $deduction->getCode());

            /**
             * @var PreOrderDeduction $orderDeduction
             */
            $orderDeduction = new PreOrderDeduction();

            $orderDeduction->init($deduction, $this->order, $orderGoodsDeductionCollection);
            return $orderDeduction;
        });

        return new OrderDeductionCollection($orderDeductions->all());
    }

    /**
     * @param $deductions
     */
    public function setDeductions(\Illuminate\Database\Eloquent\Collection $deductions)
    {
        $this->deductions = $deductions;
    }

    /**
     * @param OrderGoodsDeductionCollection $orderGoodsDeductionCollection
     */
    public function setOrderGoodsDeductionCollection(OrderGoodsDeductionCollection $orderGoodsDeductionCollection)
    {
        $this->orderGoodsDeductionCollection = $orderGoodsDeductionCollection;
    }

    /**
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductionCollection()
    {
        if (!isset($this->orderGoodsDeductionCollection)) {
            $orderGoodsDeductions = $this->order->orderGoods->flatMap(function (PreOrderGoods $orderGoods) {
                return $orderGoods->getOrderGoodsDeductions();
            });
            $this->orderGoodsDeductionCollection = new OrderGoodsDeductionCollection($orderGoodsDeductions->all());
        }
        return $this->orderGoodsDeductionCollection;
    }

    /**
     * 开启的抵扣项
     * @return Collection
     */
    private function getEnableDeductions()
    {
        if (!isset($this->deductions)) {
            /**
             * 商城开启的抵扣
             * @var Collection $deductions
             */
            $deductions = Deduction::where('enable', 1)->get();
            trace_log()->deduction('订单开启的抵扣类型', $deductions->pluck('code')->toJson());
            if ($deductions->isEmpty()) {
                return collect();
            }
            // 过滤调无效的
            $deductions = $deductions->filter(function (Deduction $deduction) {
                /**
                 * @var Deduction $deduction
                 */
                return $deduction->valid();
            });

            // 按照用户勾选顺序排序
            $sort = array_flip($this->order->getParams('deduction_ids'));
            $this->deductions = $deductions->sortBy(function ($deduction) use ($sort) {
                return array_get($sort, $deduction->code, 999);
            });
        }

        return $this->deductions;
    }

    public function getCheckedOrderDeductions()
    {
        if (!isset($this->checkedOrderDeductionCollection)) {
            // 求和订单抵扣集合中所有已选中的可用金额
            $this->checkedOrderDeductionCollection = $this->getOrderDeductions()->filter(function (PreOrderDeduction $orderDeduction) {
                return $orderDeduction->isChecked();
            });
        }


        // 返回 订单抵扣金额
        return $this->checkedOrderDeductionCollection;
    }

    /**
     * @return mixed
     */
    private function _getAmount()
    {
        // 求和订单抵扣集合中所有已选中的可用金额
        $result = $this->getOrderDeductions()->sum(function (PreOrderDeduction $orderDeduction) {
            /**
             * @var PreOrderDeduction $orderDeduction
             */
            if ($orderDeduction->isChecked()) {
                trace_log()->deduction('订单抵扣', "{$orderDeduction->getName()}获取可用金额");

                return $orderDeduction->getUsablePoint()->getMoney();
            }
            return 0;
        });

        // 返回 订单抵扣金额
        return $result;
    }

    /**
     * 获取订单抵扣金额
     * @return float
     */
    public function getAmount()
    {
        if (!isset($this->amount)) {
            $this->amount = $this->_getAmount();
            // 将抵扣总金额保存在订单优惠信息表中
            $preOrderDiscount = new PreOrderDiscount([
                'discount_code' => 'deduction',
                'amount' => $this->amount,
                'name' => '抵扣金额',

            ]);
            $preOrderDiscount->setOrder($this->order);
        }
        return $this->amount;
    }
}