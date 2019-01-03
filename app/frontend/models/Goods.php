<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/31
 * Time: 下午5:55
 */

namespace app\frontend\models;


use app\common\facades\Setting;
use app\common\modules\discount\GoodsMemberLevelDiscount;
use app\framework\Database\Eloquent\Collection;
use app\frontend\models\goods\Privilege;
use app\frontend\models\goods\Sale;
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
 * @property float deal_price
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
    public $hidden = ['content', 'description'];
    public $appends = ['vip_price'];
    private $dealPrice;
    protected $vipDiscountAmount;
    public $vipDiscountLog;

    /**
     * 获取交易价(实际参与交易的商品价格)
     * @return float|int
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function getDealPriceAttribute()
    {
        if (!isset($this->dealPrice)) {
            $level_discount_set = Setting::get('discount.all_set');
            if (
                isset($level_discount_set['type'])
                && $level_discount_set['type'] == 1
                && $this->memberLevelDiscount()->getAmount($this->market_price)
            ) {
                // 如果开启了原价计算会员折扣,并且存在等级优惠金额
                $this->dealPrice = $this->market_price;
            } else {
                // 默认使用现价
                $this->dealPrice = $this->price;
            }
        }

        return $this->dealPrice;
    }


    /**
     * @var GoodsMemberLevelDiscount
     */
    private $memberLevelDiscount;

    /**
     * @return GoodsMemberLevelDiscount
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function memberLevelDiscount()
    {
        if (!isset($this->memberLevelDiscount)) {
            $this->memberLevelDiscount = new GoodsMemberLevelDiscount($this, Member::current());
        }
        return $this->memberLevelDiscount;
    }


    /**
     * 缓存等级折金额
     *  todo 如何解决等级优惠种类记录的问题
     * @param $price
     * @return float
     * @throws \app\common\exceptions\MemberNotLoginException
     */

    public function getVipDiscountAmount($price)
    {
        if (isset($this->vipDiscountAmount)) {

            return $this->vipDiscountAmount;
        }
        $this->vipDiscountAmount = $this->memberLevelDiscount()->getAmount($price);
        $this->vipDiscountLog = $this->memberLevelDiscount()->getLog($this->vipDiscountAmount);
        return $this->vipDiscountAmount;
    }


    /**
     * 获取商品的会员价格
     * @return float|int|mixed
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function getVipPriceAttribute()
    {
        return $this->deal_price - $this->getVipDiscountAmount($this->deal_price);
    }

    public function hasOneOptions()
    {
        return $this->hasOne(GoodsOption::class);
    }

    public function hasOneSale()
    {
        return $this->hasOne(Sale::class);
    }

    /**
     * @param Builder $query
     * @param $filters
     */
    public function scopeSearch(Builder $query, $filters)
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
                    if (array_key_exists('parentid', $value) || array_key_exists('childid', $value) || array_key_exists('thirdid', $value)) {
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
                    } elseif (strpos($value, ',')) {
                        $scope = explode(',', $value);
                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', function ($join) use ($scope) {
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereIn('yz_goods_category.category_id', $scope);
                        });
                    } else {
                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', function ($join) use ($value) {
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereRaw('FIND_IN_SET(?,category_ids)', [$value]);
//                                ->where('yz_goods_category.category_id', $value);
                        });
                    }
                    break;
                case 'couponid': //搜索指定优惠券适用的商品
                    $res = Coupon::getApplicableScope($value);
                    switch ($res['type']) {
                        case Coupon::COUPON_GOODS_USE: //优惠券适用于指定商品
                            if (is_array($res['scope'])) {
                                $query->whereIn('id', $res['scope']);
                            } else {
                                $query->where('id', $res['scope']);
                            }
                            break;
                        case Coupon::COUPON_CATEGORY_USE: //优惠券适用于指定商品分类
                            if (is_array($res['scope'])) {
                                $query->join('yz_goods_category', function ($join) use ($res) {
                                    $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                        ->whereIn('yz_goods_category.category_id', $res['scope']);
                                });
                            } else {
                                $query->join('yz_goods_category', function ($join) use ($res) {
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
