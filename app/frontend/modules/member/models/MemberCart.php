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
    protected $fillable=[];
    protected $guarded = ['id'];

    /**
     * Get a list of members shopping cart through member ID
     *
     * @param int $memberId
     *
     * @return array
     * */
    public static function getMemberCartList($memberId)
    {
        $cartList = static::carts()->where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        return $cartList;
    }

    /**
     * 根据购物车id数组,获取购物车记录数组
     * @param $cartIds
     * @return mixed
     */
    public static function getCartsByIds($cartIds)
    {
        if(!is_array($cartIds)){
            $cartIds = explode(',',$cartIds);
        }
        $result = static::whereIn('id', $cartIds)
            ->get();
        return $result;
    }
    public function scopeCarts($query){
        $query->select('id', 'goods_id', 'total', 'option_id')
            ->uniacid()
            ->with(['goods' => function($query) {
                return $query->select('id', 'thumb', 'price', 'market_price', 'title');
            }])
            ->with(['goodsOption' => function ($query) {
                return $query->select('id', 'title', 'thumb', 'product_price', 'market_price');
            }]);
    }
    public function goods(){
        return $this->hasOne('app\common\models\Goods','id','goods_id');
    }

    public function goodsOption()
    {
        return $this->hasOne('app\common\models\GoodsOption','id','option_id');
    }

    public static function getMemberCartById($cartId)
    {
        return static::uniacid()->where('id', $cartId)->first();
    }

    /**
     * Get a list of members shopping cart through cart IDs
     *
     * @param array $cartIds
     *
     * @return array
     * */
    public static function getMemberCartByIds($cartIds)
    {
        return static::uniacid()->whereIn('id', $cartIds)->get()->toArray();
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

    /*
     * 检测商品是否存在购物车
     *
     * @param array $data ['member_id', 'goods_id', 'option_id']
     *
     * @return object or false
     * */
    public static function hasGoodsToMemberCart($data)
    {
        $hasGoods = self::uniacid()
            ->where([
                'member_id' => $data['member_id'],
                'goods_id'  => $data['goods_id'],
                'option_id' => $data['option_id']
            ])
            ->first();
        return $hasGoods ? $hasGoods : false;
    }

    /**
     * Remove cart items by Ids
     *
     * @param array $cartIds
     *
     * @return 1 or 0
     * */
    public static function destroyMemberCart($cartIds)
    {
        return static::uniacid()->whereIn('id', $cartIds)->delete();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'goods_id'  => '未获取到商品',
            'total'     => '商品数量不能为空',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'goods_id'  => 'required',
            'total'     => 'required',
        ];
    }

}
