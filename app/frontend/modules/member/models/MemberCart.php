<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午5:09
 */

namespace app\frontend\modules\member\models;


class MemberCart extends \app\common\models\MemberCart
{

    /**
     * Get a list of members shopping cart through member ID
     *
     * @param int $memberId
     *
     * @return array
     * */
    public static function getMemberCartList($memberId)
    {
        $list = static::select('id', 'goods_id', 'total', 'option_id')
            ->where('member_id', $memberId)
            ->uniacid()
            ->with(['getGoods' => function($query) {
                return $query->select('id', 'title');
            }])
            ->get()
            ->toArray();
        return $list;
        //return static::uniacid()->where('member_id', $memberId)->get()->toArray();
    }
    public function getGoods(){
        return $this->hasOne('app\common\models\Goods','id','goods_id');
    }
    /**
     * Get a list of members shopping cart through member ID
     *
     * @param int $cartId
     *
     * @return array
     * */
    public static function getMemberCartById($cartId)
    {
        return static::uniacid()->where('id', $cartId)->get()->toArray();
    }
    /**
     * Add merchandise to shopping cart
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    public static function storeGoodsToMemberCart($data)
    {
        //需要监听事件，购物车存在的处理方式
        return static::insert($data);
    }
    /**
     * Remove cart items by Id
     *
     * @param int $cartId
     *
     * @return 1 or 0
     * */
    public static function destroyMemberCart($cartId)
    {
        return static::uniacid()->where('id', $cartId)->delete();
    }
}
