<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\models;


use app\common\models\GoodsDiscount;
use app\frontend\modules\member\services\MemberService;

class GoodsOption extends \app\common\models\GoodsOption
{
    /**
     * 获取商品规格最终价格
     * @return mixed
     */
    public function getFinalPriceAttribute()
    {
        return $this->vip_price;
    }
    /**
     * 获取商品规格的会员价格
     * @return float
     */
    public function getVipPriceAttribute()
    {
        $result = $this->product_price;
        if (!isset($member)) {
            $member = MemberService::getCurrentMemberModel();
        }
        /**
         * @var $goodsDiscount GoodsDiscount
         */
//        dd($this->goods);
//        exit;
        $goodsDiscount = $this->goods->hasManyGoodsDiscount()->where('level_id', $member->yzMember->level_id)->first();
        if (isset($goodsDiscount)) {
            $result = $goodsDiscount->getPrice($this->product_price);
        }
        return $result;
    }
    public function goods()
    {
        return $this->belongsTo(Goods::class,'goods_id','id');
    }
}