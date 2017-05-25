<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午5:09
 */

namespace app\frontend\models;


use app\common\exceptions\AppException;

class MemberCart extends \app\common\models\MemberCart
{
    protected $fillable = [];

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
        if (!is_array($cartIds)) {
            $cartIds = explode(',', $cartIds);
        }
        $result = static::whereIn('id', $cartIds)
            ->get();

        return $result;
    }

    public function scopeCarts($query)
    {
        $query->select('id', 'goods_id', 'total', 'option_id')
            ->uniacid()
            ->with(['goods' => function ($query) {
                return $query->withTrashed()->select('id', 'thumb', 'price', 'market_price', 'title','deleted_at');
            }])
            ->with(['goodsOption' => function ($query) {
                return $query->select('id', 'title', 'thumb', 'product_price', 'market_price');
            }]);
    }


    public function goodsOption()
    {
        return $this->hasOne('app\common\models\GoodsOption', 'id', 'option_id');
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
                'goods_id' => $data['goods_id'],
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
     * @return array
     */
    public function atributeNames()
    {
        return [
            'goods_id' => '未获取到商品',
            'total' => '商品数量不能为空',
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'goods_id' => 'required',
            'total' => 'required',
        ];
    }

    /**
     * 购物车验证
     * @author shenyang
     * @throws AppException
     */
    public function validate()
    {
        if (!isset($this->goods)) {
            throw new AppException('(ID:' . $this->goods_id . ')未找到商品或已经删除');
        }

        //商品基本验证
        $this->goods->generalValidate($this->total);

        if ($this->isOption()) {
            $this->goodsOptionValidate();
        } else {
            $this->goodsValidate();
        }

    }

    /**
     * 商品购买验证
     * @throws AppException
     */
    public function goodsValidate()
    {
        if (!$this->goods->stockEnough($this->total)) {
            throw new AppException('(ID:' . $this->goods_id . ')商品库存不足');
        }
    }

    /**
     * 规格验证
     * @throws AppException
     */
    public function goodsOptionValidate()
    {
        if (!$this->goods->has_option) {
            throw new AppException('(ID:' . $this->option_id . ')商品未启用规格');
        }
        if (!isset($this->goodsOption)) {
            throw new AppException('(ID:' . $this->option_id . ')未找到商品规格或已经删除');
        }
        if (!$this->goodsOption->stockEnough($this->total)) {
            throw new AppException('(ID:' . $this->goods_id . ')商品库存不足');
        }
    }
}
