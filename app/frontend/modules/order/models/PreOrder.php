<?php

namespace app\frontend\modules\order\models;

use app\common\models\BaseModel;
use app\common\models\DispatchType;
use app\common\models\Member;
use app\common\modules\orderGoods\OrderGoodsCollection;
use app\common\requests\Request;
use app\frontend\models\Order;
use app\frontend\modules\deduction\OrderDeduction;
use app\frontend\modules\deduction\OrderDeductionCollection;
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
 * @property OrderDeductionCollection orderDeductions
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
    /**
     * @var Request
     */
    protected $request;
    protected $attributes = ['id' => null];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setRelation('orderSettings', $this->newCollection());
    }

    /**
     * @param Member $member
     * @param OrderGoodsCollection $orderGoods
     * @param Request|null $request
     * @return $this
     * @throws \app\common\exceptions\ShopException
     */
    public function init(Member $member, OrderGoodsCollection $orderGoods, Request $request = null)
    {
        $this->request = $request;
        $this->setMember($member);

        $this->beforeCreating();
        $this->setOrderGoods($orderGoods);
        /**
         * @var PreOrder $order
         */
        $this->afterCreating();

        $this->initAttributes();
        $this->initOrderGoods();
        return $this;
    }

    /**
     * @param $member
     */
    private function setMember($member)
    {
        $this->setRelation('belongsToMember', $member);
        $this->uid = $this->belongsToMember->uid;
        $this->uniacid = $this->getUniacid();
    }

    /**
     * 获取request对象
     * @return Request
     */
    public function getRequest()
    {
        if (!isset($this->request)) {
            $this->request = request();
        }
        return $this->request;
    }

    /**
     * 依赖对象传入之前
     * @throws \app\common\exceptions\ShopException
     */
    public function beforeCreating()
    {

        $this->dispatch_type_id = $this->getRequest()->input('dispatch_type_id', 0);
        /**
         * @var PreOrderAddress $orderAddress
         */
        $orderAddress = app('OrderManager')->make('PreOrderAddress');

        $orderAddress->setOrder($this);
        //临时处理，无扩展性
        if ($this->getRequest()->input('mark') !== 'undefined') {
            $this->mark = $this->getRequest()->input('mark', '');
        }

    }

    /**
     * 载入订单商品集合
     * @param OrderGoodsCollection $orderGoods
     */
    public function setOrderGoods(OrderGoodsCollection $orderGoods)
    {
        $this->setRelation('orderGoods', $orderGoods);

        $this->orderGoods->setOrder($this);

    }

    /**
     * 依赖对象传入之后
     */
    public function afterCreating()
    {

        $this->discount = new OrderDiscount($this);
        $this->orderDispatch = new OrderDispatch($this);
        $this->orderDeduction = new OrderDeduction($this);


    }

    /**
     * 初始化订单商品
     */
    public function initOrderGoods()
    {
        $this->orderGoods->each(function ($aOrderGoods) {
            /**
             * @var PreOrderGoods $aOrderGoods
             */
            $aOrderGoods->_init();
        });
    }

    /**
     * 订单优惠类
     * @return OrderDiscount
     */
    public function getDiscount()
    {
        return $this->discount;
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

    /**
     * 初始化属性
     */
    protected function initAttributes()
    {
        $attributes = array(
            'price' => $this->getPrice(),//订单最终支付价格
            'order_goods_price' => $this->getOrderGoodsPrice(),//订单商品成交价
            'goods_price' => $this->getGoodsPrice(),//订单商品原价
            'cost_amount' => $this->getCostPrice(),//订单商品原价
            'discount_price' => $this->getDiscountAmount(),//订单优惠金额
            'deduction_price' => $this->getDeductionAmount(),//订单抵扣金额
            'dispatch_price' => $this->getDispatchAmount(),//订单运费
            'is_virtual' => $this->isVirtual(),//是否是虚拟商品订单
            'goods_total' => $this->getGoodsTotal(),//订单商品总数
            'order_sn' => OrderService::createOrderSN(),//订单编号
            'create_time' => time(),
            'note' => $this->getRequest()->input('note', ''),//是否是虚拟商品订单
            'shop_name' => $this->getShopName(),//是否是虚拟商品订单
        );


        $attributes = array_merge($this->getAttributes(), $attributes);
        $this->setRawAttributes($attributes);

    }

    public function getCostPrice()
    {
        //累加所有商品数量
        $result = $this->orderGoods->sum(function (PreOrderGoods $aOrderGoods) {
            return $aOrderGoods->goods_cost_price;
        });

        return $result;
    }

    /**
     * 获取url中关于本订单的参数
     * @param null $key
     * @return mixed
     */
    public function getParams($key = null)
    {
        $result = collect(json_decode($this->getRequest()->input('orders'), true))->where('pre_id', $this->pre_id)->first();
        if (isset($key)) {
            return $result[$key];
        }

        return $result;
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
     * 计算订单成交价格
     * 外部调用只计算一次,方法内部计算过程中递归调用会返回计算过程中的金额
     * @return int
     */
    protected function getPrice()
    {
        if (array_key_exists('price', $this->attributes)) {
            // 外部调用只计算一次,方法内部计算过程中递归调用会返回计算过程中的金额

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
     * 公众号
     * @return int
     */
    private function getUniacid()
    {
        return $this->belongsToMember->uniacid;
    }

    /**
     * 店铺名
     * @return string
     */
    protected function getShopName()
    {
        return \Setting::get('shop.shop.name') ?: '平台自营';
    }

    /**
     * 统计订单商品是否有虚拟商品
     * @return bool
     */
    public function isVirtual()
    {
        if ($this->is_virtual == 1) {
            return true;
        }

        return $this->orderGoods->hasVirtual();
    }


    /**
     * @var array 需要批量更新的字段
     */
    private $batchSaveRelations = ['orderGoods', 'orderSettings', 'orderCoupons', 'orderDiscounts', 'orderDeductions'];

    /**
     * 保存关联模型
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
        /**
         * 一对一关联模型保存
         */
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
        /**
         * 多对多关联模型保存
         */
        $this->insertRelations($this->batchSaveRelations);

        return true;
    }

    /**
     * 保存每一种 多对多的关联模型集合
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

    /**
     * 保存一种 多对多的关联模型集合
     * @param $relation
     */
    private function saveManyRelations($relation)
    {
        $attributeItems = $this->$relation->map(function (BaseModel $relation) {
            $relation->updateTimestamps();

            $beforeSaving = $relation->beforeSaving();
            if ($beforeSaving === false) {
                return [];
            }
            return $relation->getAttributes();
        });

        $attributeItems = collect($attributeItems)->filter();

        $this->$relation->first()->insert($attributeItems->toArray());
        /**
         * @var Collection $ids
         */
        $ids = $this->$relation()->pluck('id');
        $this->$relation->each(function (BaseModel $item) use ($ids) {
            $item->id = $ids->shift();
            $item->afterSaving();
        });
    }


}