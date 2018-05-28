<?php
/**
 * 未生成的订单商品类
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\orderGoods\models;

use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\frontend\models\Goods;
use app\frontend\models\goods\Sale;
use app\frontend\models\GoodsOption;
use app\frontend\models\orderGoods\PreOrderGoodsDiscount;
use app\frontend\models\OrderGoods;
use app\frontend\modules\deduction\OrderGoodsDeductionCollection;
use app\frontend\modules\orderGoods\price\option\NormalOrderGoodsOptionPrice;
use app\frontend\modules\orderGoods\price\option\NormalOrderGoodsPrice;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class PreOrderGoods
 * @package app\frontend\modules\orderGoods\models
 * @property float price
 * @property float goods_price
 * @property float coupon_price
 * @property float discount_price
 * @property float $deduction_amount
 * @property float payment_amount
 * @property int goods_id
 * @property Goods goods
 * @property int id
 * @property int order_id
 * @property int uid
 * @property int total
 * @property int uniacid
 * @property int goods_option_id
 * @property string goods_option_title
 * @property GoodsOption goodsOption
 * @property OrderGoodsDeductionCollection orderGoodsDeductions
 * @property Collection orderGoodsDiscounts
 * @property Sale sale
 */
class PreOrderGoods extends OrderGoods
{
    protected $hidden = ['goods', 'sale','belongsToGood','hasOneGoodsDispatch'];
    /**
     * @var PreOrder
     */
    public $order;
    /**
     * @var Collection
     */
    public $coupons;

    /**
     * PreOrderGoods constructor.
     * @param array $attributes
     * @throws ShopException
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setPriceCalculator();
        // 订单商品优惠使用记录集合
        $this->setRelation('orderGoodsDiscounts', $this->newCollection());
        // 订单商品优惠使用记录集合
        $this->setRelation('orderGoodsDeductions', new OrderGoodsDeductionCollection());
        // 将会员等级折扣总金额保存在订单优惠信息表中
        $preOrderDiscount = new PreOrderGoodsDiscount([
            'discount_code' => 'vipDiscount',
            'amount' => $this->getVipDiscountAmount(),
            'name' => '会员等级折扣',

        ]);
        $preOrderDiscount->setOrderGoods($this);
        $attributes = $this->getPreAttributes();
        $this->setRawAttributes($attributes);
    }

    /**
     * 初始化属性,计算金额和价格,由于优惠金额的计算依赖于订单的优惠金额计算,所以需要在订单类计算完优惠金额之后,再执行这个方法
     * @throws ShopException
     */
    public function _init()
    {
        $attributes = [
            'price' => $this->getPrice(),
            'discount_price' => $this->getDiscountAmount(),
            'coupon_price' => $this->getCouponAmount()
        ];

        $attributes = array_merge($this->getAttributes(),$attributes);
        $this->setRawAttributes($attributes);

    }

    /**
     * todo 应改为魔术方法
     * @return mixed
     */
    public function getGoodsId()
    {
        return $this->goods->id;
    }

    /**
     * 为订单model提供的方法 ,设置所属的订单model
     * @param PreOrder $order
     */
    public function setOrder(PreOrder $order)
    {
        $this->order = $order;
        $this->uid = $order->uid;
        $this->uniacid = $order->uniacid;
    }


    /**
     * 与生成后的 OrderGoods 对象一致,方便外部调用
     * @return mixed
     * @throws ShopException
     */
    public function getPriceAttribute()
    {
        return $this->getPrice();
    }

    /**
     * 获取生成前的模型属性
     * @return array
     * @throws ShopException
     */
    public function getPreAttributes()
    {
        $attributes = array(
            'goods_id' => $this->goods->id,
            'goods_sn' => $this->goods->goods_sn,
            'total' => $this->total,
            'title' => $this->goods->title,
            'thumb' => $this->goods->thumb,
            'goods_price' => $this->getGoodsPrice(),
            'price' => $this->getPrice(),
            'goods_cost_price' => $this->getGoodsCostPrice(),
            'goods_market_price' => $this->getGoodsMarketPrice(),


        );

        if ($this->isOption()) {

            $attributes += [
                'goods_option_id' => $this->goodsOption->id,
                'goods_option_title' => $this->goodsOption->title,
            ];
        }

        $attributes = array_merge($this->getAttributes(), $attributes);

        return $attributes;
    }

    /**
     * 复写的push
     * @return bool
     * @throws AppException
     * @throws ShopException
     */
    public function push()
    {
        $this->save();

        // 在订单商品保存后,为它的关联模型添加外键,以便保存
        foreach ($this->relations as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];

            foreach (array_filter($models) as $model) {
                /**
                 * @var Model $model
                 */
                // 添加 order_goods_id 外键
                if (!isset($model->order_goods_id) && \Schema::hasColumn($model->getTable(), 'order_goods_id')) {
                    $model->order_goods_id = $this->id;
                }
                // 添加 order_id 外键

                if (!isset($model->order_id) && \Schema::hasColumn($model->getTable(), 'order_id')) {
                    $model->order_id = $this->order_id;
                }
            }

        }


        return parent::push();
    }

    /**
     * 显示商品数据
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();

        // 格式化价格字段,将key中带有price,amount的属性,转为保留2位小数的字符串
        $attributes = array_combine(array_keys($attributes), array_map(function ($value, $key) {
            if (str_contains($key, 'price') || str_contains($key, 'amount')) {
                $value = sprintf('%.2f', $value);
            }
            return $value;
        }, $attributes, array_keys($attributes)));

        return $attributes;
    }

    /**
     * 订单商品插入数据库
     * @param array $options
     * @return bool
     * @throws AppException
     * @throws ShopException
     */
    public function save(array $options = [])
    {
        if (!isset($this->order)) {

            throw new AppException('订单信息不存在');
        }
        $this->order_id = $this->order->id;
        $this->deduction_amount = $this->getDeductionAmount();
        $this->payment_amount = $this->getPaymentAmount();

        return parent::save($options);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ShopException
     */
    public function __get($key)
    {
        // 为了与生成后的订单商品模型一致,方便外部调用
        if ($key == 'goods_price') {
            return $this->getGoodsPrice();
        }
        return parent::__get($key);
    }

    /**
     * @var NormalOrderGoodsPrice
     */
    protected $priceCalculator;


    /**
     * 设置价格计算者
     */
    public function setPriceCalculator()
    {
        if ($this->isOption()) {
            $this->priceCalculator = new NormalOrderGoodsOptionPrice($this);

        } else {
            $this->priceCalculator = new NormalOrderGoodsPrice($this);

        }

    }

    /**
     * 获取价格计算者
     * @return NormalOrderGoodsPrice
     * @throws ShopException
     */
    protected function getPriceCalculator()
    {
        if (!isset($this->priceCalculator)) {
            throw new ShopException('订单商品计算实例不存在');
        }
        return $this->priceCalculator;
    }

    /**
     * 获取vip优惠金额
     * @return mixed
     * @throws ShopException
     */
    protected function getVipDiscountAmount()
    {
        $result = $this->getPriceCalculator()->getVipDiscountAmount();

        return $result;

    }

    /**
     * 支付金额
     * @return mixed
     * @throws ShopException
     */
    public function getPrice()
    {
        return $this->getPriceCalculator()->getPrice();
    }

    /**
     * 原始价格
     * @return mixed
     * @throws ShopException
     */
    public function getGoodsPrice()
    {
        return $this->getPriceCalculator()->getGoodsPrice();
    }

    /**
     * 原始价格
     * @return float
     * @throws ShopException
     */
    public function getPaymentAmount()
    {
        return $this->getPriceCalculator()->getPaymentAmount();
    }

    /**
     * 优惠金额
     * @return int
     * @throws ShopException
     */
    public function getDiscountAmount()
    {
        return $this->getPriceCalculator()->getDiscountAmount();

    }

    /**
     * 抵扣金额
     * @return float
     * @throws ShopException
     */
    public function getDeductionAmount()
    {
        return $this->getPriceCalculator()->getDeductionAmount();

    }

    /**
     * 优惠券金额
     * @return int
     * @throws ShopException
     */
    public function getCouponAmount()
    {
        return $this->getPriceCalculator()->getCouponAmount();

    }

    /**
     * 获取利润
     * @return mixed
     * @throws ShopException
     */
    public function getGoodsCostPrice()
    {
        return $this->getPriceCalculator()->getGoodsCostPrice();

    }

    /**
     * 市场价
     * @return mixed
     * @throws ShopException
     */
    public function getGoodsMarketPrice()
    {
        return $this->getPriceCalculator()->getGoodsMarketPrice();

    }

    public function getOrderGoodsDeductions(){
        return $this->orderGoodsDeductions;
    }

    public function getWeight(){
        if($this->isOption()){
            return $this->goodsOption->weight;
        }
        return $this->goods->weight;
    }
}