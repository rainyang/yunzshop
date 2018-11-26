<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\models;


use app\common\models\GoodsDiscount;
use app\frontend\modules\member\services\MemberService;

/**
 * Class GoodsOption
 * @package app\frontend\models
 * @property int id
 * @property int goods_id
 * @property string title
 * @property float weight
 * @property Goods goods
 */
class GoodsOption extends \app\common\models\GoodsOption
{
    public function getVipDiscountAmount(){
        return $this->goods->getVipDiscountAmount($this->product_price);
    }
    /**
     * 获取商品规格的会员价格
     * @return float
     */
    public function getVipPriceAttribute()
    {
        return $this->product_price - $this->getVipDiscountAmount();
    }
    public function goods(){
        return $this->belongsTo(Goods::class);
    }
}