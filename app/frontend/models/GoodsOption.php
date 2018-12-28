<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\models;


use app\common\facades\Setting;
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
    public function getVipDiscountAmount()
    {
        $level_discount_set = Setting::get('discount.all_set');
        if (isset($level_discount_set['type']) && $level_discount_set['type'] == 1) {
            return $this->goods->getVipDiscountAmount($this->market_price);
        }else{
            return $this->goods->getVipDiscountAmount($this->product_price);
        }
    }
    /**
     * 获取商品规格的会员价格
     * @return float
     */
    public function getVipPriceAttribute()
    {
        $level_discount_set = Setting::get('discount.all_set');
        if (isset($level_discount_set['type']) && $level_discount_set['type'] == 1) {
            return $this->market_price - $this->getVipDiscountAmount();
        }else{
            return $this->product_price - $this->getVipDiscountAmount();
        }
    }
    public function goods(){
        return $this->belongsTo(Goods::class);
    }
}