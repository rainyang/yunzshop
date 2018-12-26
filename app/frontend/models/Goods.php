<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/31
 * Time: 下午5:55
 */

namespace app\frontend\models;

use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\models\BaseModel;
use app\common\models\GoodsDiscount;
use app\framework\Database\Eloquent\Collection;
use app\frontend\models\goods\Privilege;
use app\frontend\models\goods\Sale;
use app\frontend\modules\member\services\MemberService;
use app\common\models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\StoreCashier\common\models\StoreGoods;
use Yunshop\Supplier\admin\models\SupplierGoods;

/**
 * Class Goods
 * @package app\frontend\models
 * @property int id
 * @property string goods_sn
 * @property string title
 * @property string thumb
 * @property float price
 * @property float weight
 * @property int is_plugin
 * @property int plugin_id
 * @property Sale hasOneSale
 * @property GoodsOption has_option
 * @property Privilege hasOnePrivilege
 * @property SupplierGoods supplierGoods
 * @property StoreGoods storeGoods
 * @property Collection belongsToCategorys
 * @method static self search(array $search)
 */
class Goods extends \app\common\models\Goods
{
    public $appends = ['vip_price'];
    public $hidden = ['content','description'];
    protected $vipDiscountAmount;

    public function hasOneOptions()
    {
        return $this->hasOne(GoodsOption::class);
    }

    /**
     * 缓存等级折金额
     * @param null $price
     * @return int|mixed
     * @throws AppException
     */
    public function getVipDiscountAmount($price = null){
        if(isset($this->vipDiscountAmount)){
            return $this->vipDiscountAmount;
        }
        return $this->vipDiscountAmount = $this->_getVipDiscountAmount($price);
    }

    /**
     * 默认的等级折扣商品金额
     * @return float|int
     */
    private function getDefaultVipDiscountGoodsPrice(){
        $level_discount_set = Setting::get('discount.all_set');
        if (isset($level_discount_set['type']) && $level_discount_set['type'] == 1) {
            // 如果开启了原价计算会员折扣
            $price = $this->market_price;
        }else{
            $price = $this->price;
        }
        return $price;
    }
    
    /**
     * 获取等级折扣金额
     * @param null $price
     * @return int|mixed
     * @throws AppException
     */
    protected function _getVipDiscountAmount($price = null){

        if(!isset($price)){
            $price = $this->getDefaultVipDiscountGoodsPrice();
        }
        /**
         *会员等级折扣优惠金额
         * @var $goodsDiscount GoodsDiscount
         */

        $goodsDiscount = $this->hasManyGoodsDiscount->where('level_id', Member::current()->yzMember->level_id)->first();

        if (isset($goodsDiscount)) {
            $result = $goodsDiscount->getAmount($price);
        }else{
            $result = (new GoodsDiscount())->getAmount($price);
        }
        // todo 需要提取会员等级折扣类,此处临时解决
        // 如果开启了原价计算会员折扣
        $level_discount_set = Setting::get('discount.all_set');
        if (isset($level_discount_set['type']) && $level_discount_set['type'] == 1) {
            // 补齐原价与现价的差价
            // 商品金额 = 商品现价 - （等级优惠金额 + 现价 - 原价）
            $result += $this->price - $this->market_price;
        }
        return $result;
    }

    /**
     * 获取商品的会员价格
     * @return float|int|mixed
     * @throws AppException
     */
    public function getVipPriceAttribute()
    {
        return $this->price - $this->getVipDiscountAmount();
    }



    public function hasOneSale()
    {
        return $this->hasOne(Sale::class);
    }

    public function scopeSearch(BaseModel $query, $filters)
    {
        $query->uniacid();

        if (!$filters) {
            return;
        }

        foreach ($filters as $key => $value) {
            switch ($key) {
                /*case 'category':
                    $category[] = ['id' => $value * 1];
                    $query->with("")->where('category_id', $category);
                    break;*/
                case 'keyword':
                    $query->where('title', 'LIKE', "%{$value}%");
                    break;
                case 'brand_id':
                    $query->where('brand_id', $value);
                    break;
                case 'product_attr':
                    foreach ($value as $attr) {
                        $query->where($attr, 1);
                    }
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                case 'min_price':
                    $query->where('price', '>', $value);
                    break;
                case 'max_price':
                    $query->where('price', '<', $value);
                    break;
                case 'category':
                    if(array_key_exists('parentid', $value) || array_key_exists('childid', $value) || array_key_exists('thirdid', $value)){
                        $id = $value['parentid'] ? $value['parentid'] : '';
                        $id = $value['childid'] ? $value['childid'] : $id;
                        $id = $value['thirdid'] ? $value['thirdid'] : $id;

                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')->whereRaw('FIND_IN_SET(?,category_ids)', [$id]);
                    } elseif(strpos($value, ',')){
                        $scope = explode(',', $value);
                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', function($join) use ($scope){
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereIn('yz_goods_category.category_id', $scope);
                        });
                    } else{
                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', function($join) use ($value){
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereRaw('FIND_IN_SET(?,category_ids)', [$value]);
//                                ->where('yz_goods_category.category_id', $value);
                        });
                    }
                    break;
                case 'couponid': //搜索指定优惠券适用的商品
                    $res = Coupon::getApplicableScope($value);
                    switch ($res['type']){
                        case Coupon::COUPON_GOODS_USE: //优惠券适用于指定商品
                            if(is_array($res['scope'])){
                                $query->whereIn('id', $res['scope']);
                            } else{
                                $query->where('id', $res['scope']);
                            }
                            break;
                        case Coupon::COUPON_CATEGORY_USE: //优惠券适用于指定商品分类
                            if(is_array($res['scope'])){
                                $query->join('yz_goods_category', function($join) use ($res){
                                    $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                        ->whereIn('yz_goods_category.category_id', $res['scope']);
                                });
                            } else{
                                $query->join('yz_goods_category', function($join) use ($res){
                                    $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                        ->where('yz_goods_category.category_id', $res['scope']);
                                });
                            }
                            break;
                        default: //优惠券适用于整个商城
                            break;
                    }
                    break;
                default:
                    break;
            }
        }
    }
}
