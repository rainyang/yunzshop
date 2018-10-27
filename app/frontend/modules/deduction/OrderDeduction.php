<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:01
 */

namespace app\frontend\modules\deduction;

use app\frontend\models\order\PreOrderDeduction;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Database\Eloquent\Collection;

class OrderDeduction
{
    /**
     * @var PreOrder
     */
    private $order;
    /**
     * @var Collection
     */
    private $orderDeductions;

    public function __construct(PreOrder $order)
    {
        $this->order = $order;
        // 订单抵扣使用记录集合
        $this->orderDeductions = new Collection();
        $order->setRelation('orderDeductions', $this->orderDeductions);
    }

    private function _getAmount()
    {
        /**
         * 商城开启的抵扣
         * @var Collection $deductions
         */
        $deductions = Deduction::whereEnable(1)->get();
        debug_log()->deduction('开启的抵扣类型',$deductions->pluck('code')->toJson());
        if ($deductions->isEmpty()) {
            return 0;
        }
        // 过滤调无效的
        $deductions = $deductions->filter(function ($deduction) {
            /**
             * @var Deduction $deduction
             */
            return $deduction->valid();
        });
        // todo 按照用户勾选顺序排序
        $sort = array_flip($this->order->getParams('deduction_ids'));
        $deductions = $deductions->sortBy(function ($deduction) use ($sort) {
            return array_get($sort, $deduction->code, 999);
        });

        // 遍历抵扣集合, 实例化订单抵扣类 ,向其传入订单模型和抵扣模型 返回订单抵扣集合
        $orderDeductions = $deductions->map(function ($deduction) {

            $orderDeduction = new PreOrderDeduction([], $deduction, $this->order);

            return $orderDeduction;
        });

        // 求和订单抵扣集合中所有已选中的可用金额
        $result = $orderDeductions->sum(function ($orderDeduction) {
            /**
             * @var PreOrderDeduction $orderDeduction
             */
            if ($orderDeduction->isChecked()) {
                return $orderDeduction->getUsablePoint()->getMoney();
            }
            return 0;
        });

        // 返回 订单抵扣金额
        return $result;
    }

    /**
     * todo
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
