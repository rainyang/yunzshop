<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:43
 */

namespace app\frontend\modules\member\models;



class MemberHistory extends \app\common\models\MemberHistory
{

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    public function goods()
    {
        return $this->hasOne('app\common\models\Goods','id','goods_id');
    }

    /*
     *
     * @param int memberId
     * @param int goodsId
     *
     * @return object */
    public static function getHistoryByGoodsId($memberId, $goodsId)
    {
        return static::uniacid()->where('member_id', $memberId)->where('goods_id', $goodsId)->first();
    }

    /**
     * Get member browsing records
     *
     * @param int $memberId 会员ID
     *
     * @return object $list */
    public static function getMemberHistoryList($memberId)
    {
        return MemberHistory::uniacid()
            ->where('member_id', $memberId)
            ->with(['goods' => function($query) {
                return $query->select('id', 'thumb', 'price', 'market_price', 'title');
            }])
            ->get()->toArray();
    }

}
