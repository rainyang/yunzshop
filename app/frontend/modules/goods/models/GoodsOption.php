<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\modules\goods\models;


use app\common\models\GoodsDiscount;
use app\frontend\modules\member\services\MemberService;

class GoodsOption extends \app\common\models\GoodsOption
{
    /**
     * 获取商品规格的会员价格
     * @return float
     */
    public function getVipPriceAttribute()
    {
        if (!isset($member)) {
            $member = MemberService::getCurrentMemberModel();
        }

        /**
         * @var $goodsDiscount GoodsDiscount
         */
        $goodsDiscount = $this->goods->hasManyGoodsDiscount()->where('level_id', $member->yzMember->level_id)->first();
        if (isset($goodsDiscount)) {
            //优先使用商品设置
            $result = $goodsDiscount->getPrice($this->product_price);
        }else{
            //其次等级商品全局设置
            $result = $member->yzMember->level->getMemberLevelGoodsDiscountPrice($this->product_price);
        }
        return $result;
    }
    public function goods()
    {
        return $this->belongsTo(Goods::class,'goods_id','id');
    }
}