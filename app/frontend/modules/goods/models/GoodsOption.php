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
        $goodsDiscount = $this->hasManyGoodsDiscount()->where('level_id', $member->yzMember->level_id)->first();
        if (isset($goodsDiscount)) {
            $result = $goodsDiscount->getPrice($this->price);
        }
        return $result ? $result : $this->price;
    }
    public function goods()
    {
        $this->belongsTo(Goods::class,'goods_id','id');
    }
}