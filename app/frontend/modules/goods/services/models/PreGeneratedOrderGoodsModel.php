<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\services\models;

use app\common\models\Goods;
use app\common\models\OrderGoods;

use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;


class PreGeneratedOrderGoodsModel extends OrderGoodsModel
{
    /**
     * app\frontend\modules\order\services\models\PreGeneratedOrderModel的实例
     * @var
     */
    private $Order;
    /**
     * app\common\models\Goods的实例
     * @var Goods
     */
    private $Goods;
    public $coupon_money_off_price;
    public $coupon_discount_price;

    /**
     * PreGeneratedOrderGoodsModel constructor.
     * @param Goods $goods_model
     * @param int $total
     */
    public function __construct(Goods $goods_model, $total = 1)
    {
        $this->Goods = $goods_model;
        $this->total = $total;
        parent::__construct();

    }
    protected function setGoodsDiscount()
    {
        $this->_GoodsDiscount = DiscountService::getPreOrderGoodsDiscountModel($this);
    }
    protected function setGoodsDispatch()
    {
        $this->_GoodsDispatch = DispatchService::getPreOrderGoodsDispatchModel($this);
    }
    public function getGoodsId(){
        return $this->Goods->id;
    }
    /**
     * 为订单model提供的方法 ,设置所属的订单model
     * @param PreGeneratedOrderModel $Order
     */
    public function setOrder(PreGeneratedOrderModel $Order)
    {
        $this->Order = $Order;

    }

    /**
     * 显示商品数据
     * @return array
     */
    public function toArray()
    {
        return $data = array(
            'goods_id' => $this->Goods->id,
            'goods_sn' => $this->Goods->goods_sn,
            'price' => $this->getPrice(),
            'total' => $this->total,
            'title' => $this->Goods->title,
            'thumb' => $this->Goods->thumb,
            'goods_price' => $this->Goods->price,
            'vip_price' => $this->Goods->vip_price,
            'coupon_price' => $this->getCouponPrice(),
            'coupon_discount_price' => $this->coupon_discount_price,
            'coupon_money_off_price' => $this->coupon_money_off_price,
            /*'discount_details' => $this->getDiscountDetails(),
            'dispatch_details' => $this->getDispatchDetails(),*/

        );
        return $data;
    }

    public function getCouponPrice(){
        return $this->coupon_money_off_price+$this->coupon_discount_price;
    }
    /**
     * 获取商品数量
     * @return int
     */
    public function getTotal()
    {
        return $this->total;

    }
    public function getGoodsPrice()
    {
        return $this->total * $this->Goods->price;

    }
    /**
     * 订单商品插入数据库
     * @param PreGeneratedOrderModel|null $order_model
     * @return static
     */
    public function generate(PreGeneratedOrderModel $order_model = null)
    {
        if (isset($order_model)) {
            $this->setOrder($order_model);
        }

        $data = array(
            'goods_price' => $this->getGoodsPrice(),
            'discount_price' => $this->getDiscountPrice(),
            'price' => $this->getPrice(),
            'goods_id' => $this->Goods->id,
            'total' => $this->getTotal(),
            'goods_sn' => $this->Goods->goods_sn,
            'title' => $this->Goods->title,
            'thumb' => $this->Goods->thumb,
            'uid' => $this->Order->getMemberModel()->uid,
            'order_id' => $this->Order->id,
            'uniacid' => $this->Order->getShopModel()->uniacid,
        );
        dump('订单商品插入数据为');
        dump($data);
        //return;
        return OrderGoods::create($data);
    }
    public function getVipPrice(){
        return $this->Goods->vip_price;
    }
    /**
     * @param $name
     * @return null
     */
    //todo 在确认没有其他类调用后,删除这个方法
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
}