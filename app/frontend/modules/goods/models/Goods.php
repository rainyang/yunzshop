<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/31
 * Time: 下午5:55
 */

namespace app\frontend\modules\goods\models;

use app\common\models\GoodsDiscount;
use app\frontend\modules\member\services\MemberService;

class Goods extends \app\common\models\Goods
{
    public $appends = ['vip_price'];

    public function hasOneOptions()
    {
        return $this->hasOne('app\common\models\GoodsOption');
    }

    /**
     * 获取商品的会员价格
     * @return float
     */
    public function getVipPriceAttribute()
    {
        $result = $this->price;
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
        return $result;
    }
}