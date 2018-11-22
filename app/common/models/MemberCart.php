<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:47
 */

namespace app\common\models;

use app\common\exceptions\AppException;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MemberCart
 * @package app\common\models
 * @property int plugin_id
 * @property int option_id
 * @property int total
 * @property int goods_id
 * @property Goods goods
 * @property GoodsOption goodsOption
 */
class MemberCart extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_member_cart';

    public function isOption()
    {
        return !empty($this->option_id);
    }

    public function goods()
    {
        return $this->belongsTo(app('GoodsManager')->make('Goods'));
    }

    /**
     * 购物车验证
     * @throws AppException
     */
    public function validate()
    {
        if (!isset($this->goods)) {
            throw new AppException('(ID:' . $this->goods_id . ')未找到商品或已经删除');
        }

        //$this->getAllMemberCarts()->validate();
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