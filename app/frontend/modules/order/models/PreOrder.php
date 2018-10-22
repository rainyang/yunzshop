<?php

namespace app\frontend\modules\order\models;

use app\common\events\order\AfterPreOrderLoadOrderGoodsEvent;
use app\common\exceptions\AppException;
use app\common\models\BaseModel;
use app\common\models\DispatchType;
use app\frontend\models\Member;
use app\frontend\models\Order;
use app\frontend\modules\deduction\OrderDeduction;
use app\frontend\modules\dispatch\models\OrderDispatch;
use app\frontend\modules\dispatch\models\PreOrderAddress;
use app\frontend\modules\order\OrderDiscount;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use Illuminate\Support\Collection;

/**
 * 订单生成类
 * Class preOrder
 * @package app\frontend\modules\order\services\models
 * @property Collection orderDeductions
 * @property Collection orderDiscounts
 * @property Collection orderCoupons
 * @property Collection orderSettings
 * @property int id
 * @property string mark
 * @property string pre_id
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
 * @property PreOrderAddress orderAddress
 * @property int uniacid
 * @property PreOrderGoodsCollection orderGoods
 * @property Member belongsToMember
 * @property DispatchType hasOneDispatchType
 */
class PreOrder extends Order
{
    use PreOrderTrait;
    protected $appends = ['pre_id'];
    protected $hidden = ['belongsToMember'];
    /**
     * @var OrderDispatch 运费类
     */
    protected $orderDispatch;
    /**
     * @var OrderDiscount 优惠类
     */
    protected $discount;
    /**
     * @var OrderDeduction 抵扣类
     */
    protected $orderDeduction;
    protected $attributes = ['id' => null];

    /**
     * PreOrder constructor.
     * @param array $attributes
     * @throws \app\common\exceptions\ShopException
     */
    public function __construct(array $attributes = [])
    {
        $this->dispatch_type_id = request()->input('dispatch_type_id', 0);
        parent::__construct($attributes);

        $orderAddress = new PreOrderAddress();
        $orderAddress->setOrder($this);
        //临时处理，无扩展性
        if (request()->input('mark') !== 'undefined') {
            $this->mark = request()->input('mark', '');
        }

    }

    public function setOrderGoods(PreOrderGoodsCollection $orderGoods)
    {
        $this->setRelation('orderGoods', $orderGoods);

        $this->orderGoods->setOrder($this);

        event(new AfterPreOrderLoadOrderGoodsEvent($this));


    }

    public function _init()
    {

        $this->setRelation('orderSettings', $this->newCollection());

        $this->discount = new OrderDiscount($this);
        $this->orderDispatch = new OrderDispatch($this);
        $this->orderDeduction = new OrderDeduction($this);

        $attributes = $this->getPreAttributes();
        $this->setRawAttributes($attributes);

        $this->orderGoods->each(function ($aOrderGoods) {
            /**
             * @var PreOrderGoods $aOrderGoods
             */
            $aOrderGoods->_init();
        });
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * 计算订单优惠金额
     * @return number
     */
    protected function getDiscountAmount()
    {
        return $this->discount->getAmount();
    }

    /**
     * 获取订单抵扣金额
     * @return number
     */
    protected function getDeductionAmount()
    {
        return $this->orderDeduction->getAmount();
    }

    /**
     * 计算订单运费
     * @return int|number
     */
    public function getDispatchAmount()
    {

        return $this->orderDispatch->getFreight();
    }

    /**
     * 订单生成前 分组订单的标识(规则: 将goods_id 排序之后用a连接)
     * @return string
     */
    public function getPreIdAttribute()
    {
        return md5($this->orderGoods->pluck('goods_id')->toJson());
    }

    /**
     * 获取url中关于本订单的参数
     * @param null $key
     * @return mixed
     */
    public function getParams($key = null)
    {
        $result = collect(json_decode(request()->input('orders'), true))->where('pre_id', $this->pre_id)->first();
        if (isset($key)) {
            return $result[$key];
        }

        return $result;
    }

    /**
     * 显示订单数据
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes = $this->formatAmountAttributes($attributes);
        return $attributes;
    }

    public function getPreAttributes()
    {
        $attributes = array(
            'price' => $this->getPrice(),//订单最终支付价格
            'order_goods_price' => $this->getOrderGoodsPrice(),//订单商品成交价
            'goods_price' => $this->getGoodsPrice(),//订单商品原价
            'discount_price' => $this->getDiscountAmount(),//订单优惠金额
            'deduction_price' => $this->getDeductionAmount(),//订单抵扣金额
            'dispatch_price' => $this->getDispatchAmount(),//订单运费
            'goods_total' => $this->getGoodsTotal(),//订单商品总数
            'order_sn' => OrderService::createOrderSN(),//订单编号
            'create_time' => time(),
            'uid' => $this->uid,
            'uniacid' => $this->uniacid,
            'is_virtual' => $this->isVirtual(),//是否是虚拟商品订单
        );


        $attributes = array_merge($this->getAttributes(), $attributes);
        return $attributes;
    }

    /**
     * 保存一种关联模型集合
     * @param $relation
     */
    private function saveManyRelations($relation)
    {
        $attributeItems = $this->$relation->map(function (BaseModel $relation) {
            $relation->updateTimestamps();
            return $relation->getAttributes();
        });
        $this->$relation->first()->insert($attributeItems->toArray());

    }

    /**
     * 保存关联模型集合
     * @param array $relations
     */
    private function insertRelations($relations = [])
    {
        foreach ($relations as $relation) {
            if ($this->$relation->isNotEmpty()) {
                $this->saveManyRelations($relation);
            }
        }
    }

    private $batchSaveRelations = ['orderGoods', 'orderSettings', 'orderCoupons', 'orderDiscounts', 'orderDeductions'];

    /**
     * @return bool
     * @throws \Exception
     */
    public function push()
    {
        foreach ($this->relations as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];
            /**
             * @var BaseModel $model
             */
            foreach (array_filter($models) as $model) {
                if (!isset($model->order_id) && $model->hasColumn('order_id')) {
                    $model->order_id = $this->id;
                }
            }
        }
        $this->insertRelations($this->batchSaveRelations);

        $relations = array_except($this->relations, $this->batchSaveRelations);

        foreach ($relations as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];

            foreach (array_filter($models) as $model) {
                if (!$model->push()) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * 统计订单商品是否有虚拟商品
     * @return bool
     */
    public function isVirtual()
    {
        return $this->orderGoods->hasVirtual();
    }

    /**
     * 计算订单成交价格
     * @return int
     */
    protected function getPrice()
    {
        if (array_key_exists('price', $this->attributes)) {
            // 一次计算内避免循环调用,返回计算过程中的价格
            return $this->price;
        }

        //订单最终价格 = 商品最终价格 - 订单优惠 + 订单运费 - 订单抵扣
        $this->price = $this->getOrderGoodsPrice();

        // todo 为了保证每一项优惠计算之后,立刻修改price ,临时修改成这样.需要想办法重写
        $this->getDiscountAmount();

        $this->price += $this->getDispatchAmount();

        $this->price -= $this->getDeductionAmount();

        return max($this->price, 0);
    }


}