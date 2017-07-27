<?php

namespace app\frontend\modules\order\models;

use app\common\exceptions\AppException;

use app\frontend\models\Order;
use app\frontend\modules\discount\models\OrderDiscount;
use app\frontend\modules\dispatch\models\OrderDispatch;
use app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * 订单生成类
 * 输入
 *  用户model
 *  店铺model
 *  未生成的订单商品(实例)app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods
 * 输出
 *  预下单信息
 *  订单表插入结果
 * 执行
 *  订单表操作
 * 事件通知
 *  终止订单生成
 *  订单生成后
 * Class preGeneratedOrder
 * @package app\frontend\modules\order\services\models
 * @property Collection orderDeductions
 * @property Collection orderCoupons
 */
class PreGeneratedOrder extends Order
{
    protected $appends = [];

    /**
     * @var OrderDispatch 运费类
     */
    protected $orderDispatch;
    /**
     * @var OrderDiscount 优惠类
     */
    protected $orderDiscount;

    public function setOrderGoods(Collection $orderGoods)
    {
        $this->setRelation('orderGoods', $this->newCollection());

        $orderGoods->each(function ($aOrderGoods) {
            /**
             * @var PreGeneratedOrderGoods $aOrderGoods
             */

            $this->orderGoods->push($aOrderGoods);

            $aOrderGoods->setOrder($this);
        });

        $this->setDispatch();
        $this->setDiscount();
    }

    protected function setDiscount()
    {
        $this->orderDiscount = new OrderDiscount($this);
    }

    protected function setDispatch()
    {
        $this->orderDispatch = new OrderDispatch($this);
    }

    /**
     * 对外提供的获取订单商品方法
     * @return Collection
     */
    public function getOrderGoodsModels()
    {
        return $this->orderGoods;
    }

    public function getOrder()
    {
        return $this;
    }

    public function getMember()
    {
        return $this->belongsToMember;
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
     * 订单生成前 分组订单的标识(规则: 将goods_id 排序之后用a连接)
     * @return string
     */
    private function getPreId()
    {
        return $this->getOrderGoodsModels()->pluck('goods_id')->sort()->implode('a');
    }

    /**
     * 获取url中关于本订单的参数
     * @param null $key
     * @return mixed
     */
    public function getParams($key = null)
    {
        $result = collect(json_decode(\Request::input('orders'), true))->where('pre_id', $this->getPreId())->first();
        if (isset($key)) {
            return $result[$key];
        }

        return $result;
    }

    public function getPreAttributes()
    {
        return array(
            'pre_id' => $this->getPreId(),
            'price' => sprintf('%.2f', $this->getPrice()),
            'goods_price' => sprintf('%.2f', $this->getFinalPrice()),
            'dispatch_price' => sprintf('%.2f', $this->getDispatchPrice()),
            'discount_price' => sprintf('%.2f', $this->getDiscountPrice()),
            'deduction_price' => sprintf('%.2f', $this->getDeductionPrice()),

        );
    }

    /**
     * 输出订单信息
     * @return array
     */
    public function toArray()
    {
        $this->setRawAttributes(array_merge($this->getAttributes(),$this->getPreAttributes()));

        return parent::toArray();
    }

    public function push()
    {
        foreach ($this->relations as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];

            foreach (array_filter($models) as $model) {
                if (!isset($model->order_id) && Schema::hasColumn($model->getTable(), 'order_id')) {
                    $model->order_id = $this->id;
                }
            }
        }

        return parent::push();
    }

    /**
     * 订单插入数据库,触发订单生成事件
     * @return mixed
     * @throws AppException
     */
    public function generate()
    {
        $orderAttributes = array_merge($this->getAttributes(),$this->createOrder());
        //$this->get
        $this->setRawAttributes($orderAttributes);
        $this->save();

        $result = $this->push();

//        if($result === false){
//            throw new AppException('订单相关信息保存失败');
//        }
//        //$orderGoodsModels = $this->createOrderGoods();
//        dd($this);
//
//        $order = Order::create($orderModel);
//        exit;
//        $order->push();
////        foreach ($orderGoodsModels as $orderGoodsModel) {
////            $orderGoodsModel->order_id = $order->id;
////            $orderGoodsModel->save();
////        }

        //$this->id = $order->id;
        return $this->id;
    }


    /**
     * 订单插入数据库
     * @return array 新生成的order model
     */
    private function createOrder()
    {
        $data = array(
            'price' => $this->getPrice(),//订单最终支付价格
            'order_goods_price' => $this->getOrderGoodsPrice(),//订单商品商城价
            'goods_price' => $this->getFinalPrice(),//订单会员价
            'discount_price' => $this->getDiscountPrice(),//订单优惠金额
            'deduction_price' => $this->getDeductionPrice(),//订单抵扣金额
            'dispatch_price' => $this->getDispatchPrice(),//订单运费
            'goods_total' => $this->getGoodsTotal(),//订单商品总数
            'order_sn' => OrderService::createOrderSN(),//订单编号
            'create_time' => time(),
            //配送类获取订单配送方式id
            'dispatch_type_id' => 0,
            'uid' => $this->uid,
            'uniacid' => $this->uniacid,
        );

        return $data;
    }

    /**
     * 统计商品总数
     * @return int
     */
    protected function getGoodsTotal()
    {
        //累加所有商品数量
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->total;
        });

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

        $result = max($this->getFinalPrice() - $this->getDiscountPrice() - $this->getDeductionPrice() + $this->getDispatchPrice(), 0);

        return $result;
    }

    /**
     * 统计订单商品小计金额
     * @return int
     */
    protected function getFinalPrice()
    {
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->getFinalPrice();
        });

        return $result;
    }

    /**
     * 统计订单商品成交金额
     * @return int
     */
    protected function getOrderGoodsPrice()
    {
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->getPrice();
        });
        return $result;
    }

    public function __get($key)
    {
        return parent::__get($key); // TODO: Change the autogenerated stub
    }
}