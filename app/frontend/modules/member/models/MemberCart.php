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
        $cartList = static::select('id', 'goods_id', 'total', 'option_id')
            ->where('member_id', $memberId)
            ->uniacid()
            ->with(['goods' => function($query) {
                return $query->select('id', 'thumb', 'price', 'market_price', 'title');
            }])
            ->with(['goodsOption' => function ($query) {
                return $query->select('id', 'title', 'thumb', 'product_price', 'market_price');
            }])
            ->get()
            ->toArray();
        return $cartList;
        //return static::uniacid()->where('member_id', $memberId)->get()->toArray();
    }
    public function goods(){
        return $this->hasOne('app\common\models\Goods','id','goods_id');
    }
    public function goodsOption()
    {
        return $this->hasOne('app\common\models\GoodsOption','id','option_id');
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
