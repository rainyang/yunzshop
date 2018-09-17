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
use app\common\models\BaseModel;
use app\frontend\models\Goods;
use app\frontend\models\goods\Sale;
use app\frontend\models\GoodsOption;
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
    use PreOrderGoodsTrait;
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
     * PreOrderGoods constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // 订单商品优惠使用记录集合
        $this->setRelation('orderGoodsDiscounts', $this->newCollection());
        // 订单商品优惠使用记录集合
        $this->setRelation('orderGoodsDeductions', new OrderGoodsDeductionCollection());
        
        $attributes = $this->getPreAttributes();
        $this->setRawAttributes($attributes);
    }

    /**
     * 初始化属性,计算金额和价格,由于优惠金额的计算依赖于订单的优惠金额计算,所以需要在订单类计算完优惠金额之后,再执行这个方法
     */
    public function _init()
    {
        $attributes = $this->getPreAttributes();
        $this->setRawAttributes($attributes);
        $attributes = [
            'price' => $this->getPrice(),
            'coupon_price' => $this->getCouponAmount()
        ];

        $attributes = array_merge($this->getAttributes(),$attributes);
        $this->setRawAttributes($attributes);

    }



    /**
     * 与生成后的 OrderGoods 对象一致,方便外部调用
     * @return mixed
     */
    public function getPriceAttribute()
    {
        return $this->getPrice();
    }


    /**
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
                 * @var BaseModel $model
                 */
                // 添加 order_goods_id 外键
                if (!isset($model->order_goods_id) && $model->hasColumn('order_goods_id')) {
                    $model->order_goods_id = $this->id;
                }
                // 添加 order_id 外键

                if (!isset($model->order_id) && $model->hasColumn('order_id')) {
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
        $attributes['payment_amount'] = $this->getPaymentAmount();
        $attributes['deduction_amount'] = $this->getDeductionAmount();
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
    public function _getPriceCalculator()
    {
        if ($this->isOption()) {
            $priceCalculator = new NormalOrderGoodsOptionPrice($this);

        } else {
            $priceCalculator = new NormalOrderGoodsPrice($this);
        }
        return $priceCalculator;
    }

    /**
     * 获取价格计算者
     * @return NormalOrderGoodsPrice
     */
    protected function getPriceCalculator()
    {
        if (!isset($this->priceCalculator)) {
            $this->priceCalculator = $this->_getPriceCalculator();
        }
        return $this->priceCalculator;
    }

    /**
     * 获取vip优惠金额
     * @return mixed
     */
    protected function getVipDiscountAmount()
    {
        $result = $this->getPriceCalculator()->getVipDiscountAmount();

        return $result;

    }


    /**
     * 均摊的支付金额
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->getPriceCalculator()->getPaymentAmount();
    }

    /**
     * 抵扣金额
     * @return float
     */
    public function getDeductionAmount()
    {
        return $this->getPriceCalculator()->getDeductionAmount();

    }

    /**
     * 优惠券金额
     * @return int
     */
    public function getCouponAmount()
    {
        return $this->getPriceCalculator()->getCouponAmount();

    }


}