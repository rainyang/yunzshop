<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\models;

use app\common\facades\Setting;

/**
 * Class GoodsOption
 * @package app\frontend\models
 * @property int id
 * @property int goods_id
 * @property string title
 * @property float weight
 * @property float product_price
 * @property float market_price
 * @property float cost_price
 * @property Goods goods
 */
class GoodsOption extends \app\common\models\GoodsOption
{
    /**
     * 获取交易价(实际参与交易的商品价格)
     * @return float|mixed
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function getDealPriceAttribute(){
        if (!isset($this->dealPrice)) {
            $level_discount_set = Setting::get('discount.all_set');
            if (
                isset($level_discount_set['type'])
                && $level_discount_set['type'] == 1
                && $this->goods->_getVipDiscountAmount($this->product_price)
            ) {
                // 如果开启了原价计算会员折扣,并且存在等级优惠金额
                $this->dealPrice = $this->market_price;
            } else {
                // 默认使用现价
                $this->dealPrice = $this->price;
            }
        }

        return $this->dealPrice;
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}