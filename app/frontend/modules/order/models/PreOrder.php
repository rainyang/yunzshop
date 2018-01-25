<?php

namespace app\frontend\modules\order\models;

use app\common\exceptions\AppException;
use app\frontend\models\Order;
use app\frontend\modules\discount\models\OrderDiscount;
use app\frontend\modules\dispatch\models\OrderDispatch;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * 订单生成类
 * Class preOrder
 * @package app\frontend\modules\order\services\models
 * @property Collection orderDeductions
 * @property Collection orderCoupons
 * @property Collection orderSettings
 * @property int id
 * @property float price
 * @property float goods_price
 * @property float order_goods_price
 * @property float discount_price
 * @property float deduction_price
 * @property float dispatch_price
 * @property int goods_total
 * @property string order_sn
 * @property int create_time
 * @property int uid
 * @property int uniacid
 * @property PreOrderGoodsCollection orderGoods
 */
class PreOrder extends Order
{
    protected $appends = ['pre_id'];
    protected $hidden = ['belongsToMember'];
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
        $this->setRelation('orderGoods', new PreOrderGoodsCollection());

        $orderGoods->each(function ($aOrderGoods) {
            /**
             * @var PreOrderGoods $aOrderGoods
             */

            $this->orderGoods->push($aOrderGoods);

            $aOrderGoods->setOrder($this);
        });

        $this->setDispatch();
        $this->setDiscount();

    }

    public function __construct(array $attributes = [])
    {
        $this->dispatch_type_id = request()->input('dispatch_type_id', 0);

        parent::__construct($attributes);
        $this->setRelation('orderSettings',$this->newCollection());

    }

    public function _init()
    {
        $attributes = $this->getPreAttributes();
        $this->setRawAttributes($attributes);
        $this->orderGoods->each(function ($aOrderGoods) {
            /**
             * @var PreOrderGoods $aOrderGoods
             */
            $aOrderGoods->_init();
        });
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
    protected function getDiscountAmount()
    {
        return $this->orderDiscount->getDiscountAmount();
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
    public function getDispatchPrice()
    {
        return $this->orderDispatch->getDispatchPrice();
    }

    /**
     * 订单生成前 分组订单的标识(规则: 将goods_id 排序之后用a连接)
     * @return string
     */
    public function getPreIdAttribute()
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
        $result = collect(json_decode(\Request::input('orders'), true))->where('pre_id', $this->pre_id)->first();
        if (isset($key)) {
            return $result[$key];
        }

        return $result;
    }

    public function getPreAttributes()
    {
        $attributes = array(
            'price' => $this->getPrice(),//订单最终支付价格
            'order_goods_price' => $this->getOrderGoodsPrice(),//订单商品成交价
            'goods_price' => $this->getGoodsPrice(),//订单商品原价
            'discount_price' => $this->getDiscountAmount(),//订单优惠金额
            'deduction_price' => $this->getDeductionPrice(),//订单抵扣金额
            'dispatch_price' => $this->getDispatchPrice(),//订单运费
            'goods_total' => $this->getGoodsTotal(),//订单商品总数
            'order_sn' => OrderService::createOrderSN(),//订单编号
            'create_time' => time(),
            'uid' => $this->uid,
            'uniacid' => $this->uniacid,
        );

        $attributes = array_merge($this->getAttributes(), $attributes);

        return $attributes;
    }

    /**
     * 显示订单数据
     * @return array
     */
    public function toArray()
    {
        $attributes = $this->getAttributes();
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
        $this->save();

        $result = $this->push();

        if($result === false){

            throw new AppException('订单相关信息保存失败');
        }
        return $this->id;
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
     */
    protected function getPrice()
    {
        if(isset($this->price)){
            return $this->price;
        }

        //订单最终价格 = 商品最终价格 - 订单优惠 - 订单抵扣 + 订单运费
        $this->price = max($this->getOrderGoodsPrice() - $this->getDiscountAmount() + $this->getDispatchPrice(), 0);
        $this->price = $this->price - $this->getDeductionPrice();
        return $this->price;
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

        //订单属性添加商品价格属性
        $this->goods_price = $result;

        return $result;
    }

    /**
     * 统计订单商品原价
     * @return int
     */
    protected function getGoodsPrice()
    {
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->getGoodsPrice();
        });
        return $result;
    }

    public function __get($key)
    {
        return parent::__get($key); // TODO: Change the autogenerated stub
    }
}