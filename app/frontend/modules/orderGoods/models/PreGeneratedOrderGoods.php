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
use app\frontend\models\OrderGoods;
use app\frontend\modules\orderGoods\price\OrderGoodsPriceCalculator;
use app\frontend\modules\order\models\PreGeneratedOrder;
use Illuminate\Support\Collection;
use Yunshop\Love\Frontend\Models\LoveOrderGoods;

class PreGeneratedOrderGoods extends OrderGoods
{

    /**
     * @var PreGeneratedOrder
     */
    public $order;
    public $coupons;
    /**
     * todo 防止toArray方法死循环,order对象应该直接以属性赋值,而不是setRelation设置关联模型
     * @var array
     */
    protected $hidden = ['order'];

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
     * @param PreGeneratedOrder $order
     */
    public function setOrder(PreGeneratedOrder $order)
    {
        $this->order = $order;
        $this->uid = $order->uid;
        $this->uniacid = $order->uniacid;
    }


    /**
     * 与生成后的 OrderGoods 对象一致,方便外部调用
     * @return int
     */
    public function getPriceAttribute()
    {
        return $this->getPrice();
    }

    /**
     * 获取生成前的模型属性
     * @return array
     */
    public function getPreAttributes()
    {
        $attributes = array(
            'goods_id' => $this->goods->id,
            'goods_sn' => $this->goods->goods_sn,
            'price' => $this->getPrice(),
            'vip_price' => $this->getFinalPrice(),
            'total' => $this->total,
            'title' => $this->goods->title,
            'thumb' => $this->goods->thumb,
            'goods_price' => $this->getGoodsPrice(),
            'goods_cost_price' => $this->getGoodsCostPrice(),
            'goods_market_price' => $this->getGoodsMarketPrice(),
            'discount_price' => $this->getDiscountPrice(),
            'coupon_price' => $this->getCouponPrice(),
        );
        if (isset($this->goodsOption)) {

            $attributes += [
                'goods_option_id' => $this->goodsOption->id,
                'goods_option_title' => $this->goodsOption->title,
            ];
        }

        $attributes = array_merge($this->getAttributes(),$attributes);

        return $attributes;
    }

    /**
     * 复写的push
     * @return bool
     */
    public function push()
    {
        $this->save();

        // 在订单商品保存后,为它的关联模型添加外键,以便保存
        foreach ($this->relations as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];

            foreach (array_filter($models) as $model) {
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
        $attributes = $this->getPreAttributes();
        // 格式化价格字段,将key中带有price,amount的属性,转为保留2位小数的字符串
        $attributes = array_combine(array_keys($attributes), array_map(function ($value, $key) {
            if (strpos($key, 'price') || strpos($key, 'amount')) {
                $value = sprintf('%.2f', $value);
            }
            return $value;
        }, $attributes, array_keys($attributes)));
        $this->setRawAttributes($attributes);

        return parent::toArray();
    }

    /**
     * 订单商品插入数据库
     * @param array $options
     * @return bool
     * @throws AppException
     */
    public function save(array $options = [])
    {
        if (!isset($this->order)) {

            throw new AppException('订单信息不存在');
        }
        $this->order_id = $this->order->id;
        $attributes = $this->getPreAttributes();

        $this->setRawAttributes($attributes);

        return parent::save($options);
    }

    public function __get($key)
    {
        // 为了与生成后的订单商品模型一致,方便外部调用
        if ($key == 'goods_price') {
            return $this->getGoodsPrice();
        }
        return parent::__get($key);
    }

    /**
     * @var OrderGoodsPriceCalculator
     */
    protected $priceCalculator;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setPriceCalculator($this);
    }

    /**
     * 设置价格计算者
     * @param $orderGoods
     */
    public function setPriceCalculator($orderGoods)
    {
        $this->priceCalculator = new OrderGoodsPriceCalculator($orderGoods);

    }

    /**
     * 添加价格装饰器
     * @param $callback
     */
    public function pushPriceDecorator($callback)
    {
        $this->priceCalculator->pushDecorator($callback);
    }

    /**
     * 获取价格计算者
     * @return OrderGoodsPriceCalculator
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
     * 支付金额
     * @return int
     */
    public function getPrice()
    {
        return $this->getPriceCalculator()->getPrice();
    }

    /**
     * 原始价格
     */
    public function getGoodsPrice()
    {
        return $this->getPriceCalculator()->getGoodsPrice();


    }

    /**
     * 销售价格
     */
    public function getFinalPrice()
    {
        return $this->getPriceCalculator()->getFinalPrice();

    }

    /**
     * 优惠金额
     */
    public function getDiscountPrice()
    {
        return $this->getPriceCalculator()->getDiscountPrice();

    }

    /**
     * 优惠券金额
     * @return int
     */
    public function getCouponPrice()
    {
        return $this->getPriceCalculator()->getCouponPrice();

    }

    /**
     * 计算单品满减价格
     * @return mixed
     */
    protected function getFullPriceReductions()
    {
        //return $this->getPriceCalculator()->getFullPriceReductions();

    }

    /**
     * 获取利润
     * @return mixed
     */
    public function getGoodsCostPrice()
    {
        return $this->getPriceCalculator()->getGoodsCostPrice();

    }

    /**
     * 市场价
     * @return mixed
     */
    public function getGoodsMarketPrice()
    {
        return $this->getPriceCalculator()->getGoodsMarketPrice();

    }

}