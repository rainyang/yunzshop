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
     * @var PreGeneratedOrderModel
     */
    protected $order;

    public $couponMoneyOffPrice;
    public $couponDiscountPrice;

    public function __construct(array $attributes = [])
    {
        if (isset($attributes['option_id'])) {
            $attributes['goods_option_id'] = $attributes['option_id'];
            unset($attributes['option_id']);
        }
        parent::__construct($attributes);
        $this->setGoodsDiscount();
        $this->setGoodsDispatch();
    }

    protected function setGoodsDiscount()
    {
        $this->goodsDiscount = DiscountService::getPreOrderGoodsDiscountModel($this);
    }

    protected function setGoodsDispatch()
    {
        $this->goodsDispatch = DispatchService::getPreOrderGoodsDispatchModel($this);
    }

    public function getGoodsId()
    {
        return $this->goods->id;
    }

    /**
     * 显示商品数据
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'goods_id' => $this->goods->id,
            'goods_sn' => $this->goods->goods_sn,
            'price' => $this->getPrice(),
            'total' => $this->total,
            'title' => $this->goods->title,
            'thumb' => $this->goods->thumb,
            'goods_option_id' => $this->goodsOption->id,
            'goods_option_title' => $this->goodsOption->title,
            'goods_price' => $this->getGoodsPrice(),
            'vip_price' => $this->getVipPrice(),
            'coupon_price' => $this->getCouponPrice(),
            'coupon_discount_price' => $this->couponDiscountPrice,
            'coupon_money_off_price' => $this->couponMoneyOffPrice,
        );
        if (isset($this->goodsOption)) {
            $data += [
                'goods_option_id' => $this->goodsOption->id,
                'goods_option_title' => $this->goodsOption->title,
            ];
        }
        return $data;
    }

    public function getCouponPrice()
    {
        return $this->couponMoneyOffPrice + $this->couponDiscountPrice;
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
        //dd($this);

        if (isset($this->goodsOption)) {
            return $this->goodsOption->product_price * $this->getTotal();
        }
        return $this->getTotal() * $this->goods->price;

    }

    /**
     * 订单商品插入数据库
     * @param PreGeneratedOrderModel|null $orderModel
     * @return static
     */
    public function generate(PreGeneratedOrderModel $orderModel = null)
    {
        if (isset($orderModel)) {
            $this->setOrder($orderModel);
        }

        $data = array(
            'goods_price' => $this->getGoodsPrice(),
            'discount_price' => $this->getDiscountPrice(),
            'price' => $this->getPrice(),
            'goods_id' => $this->goods->id,
            'total' => $this->getTotal(),
            'goods_sn' => $this->goods->goods_sn,
            'title' => $this->goods->title,
            'thumb' => $this->goods->thumb,
            'uid' => $this->order->getMember()->uid,
            'order_id' => $this->order->id,
            'uniacid' => $this->order->uniacid,
        );
        if (isset($this->goodsOption)) {
            $data += [
                'goods_option_id' => $this->goodsOption->id,
                'goods_option_title' => $this->goodsOption->title,
            ];
        }
        return new OrderGoods($data);
    }

    protected function getDiscountPrice()
    {
        return $this->getCouponPrice();

    }

    public function getVipPrice()
    {
        if (isset($this->goodsOption)) {

            return $this->goodsOption->product_price * $this->getTotal();
        }
        return $this->goods->vip_price * $this->getTotal();
    }

}