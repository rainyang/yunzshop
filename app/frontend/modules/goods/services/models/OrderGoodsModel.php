<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午3:36
 */

namespace app\frontend\modules\goods\services\models;


abstract class OrderGoodsModel
{
    /**
     * @var \app\frontend\modules\dispatch\services\models\GoodsDispatch 的实例
     */
    protected $_GoodsDispatch;
    /**
     * @var \app\frontend\modules\discount\services\models\GoodsDiscount 的实例
     */
    protected $_GoodsDiscount;
    protected $total;

    public function __construct()
    {

    }

    /**
     * 设置商品数量
     * @param $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }
    /**
     * 计算成交价格
     * @return int
     */
    public function getPrice()
    {
        //成交价格=商品销售价+优惠价格
        $result = max($this->getGoodsPrice() + $this->getDiscountPrice(),0);
        return $result;
    }

    /**
     * 计算商品销售价格
     * @return int
     */
    abstract function getGoodsPrice();

    /**
     * 计算商品优惠价格
     * @return number
     */
    protected function getDiscountPrice()
    {
        return $this->coupon_discount_price;

    }

}