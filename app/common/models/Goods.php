<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\backend\modules\goods\models\Sale;
use app\common\exceptions\AppException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use app\common\models\Coupon;

class Goods extends BaseModel
{

    use SoftDeletes;

    public $table = 'yz_goods';
    public $attributes = ['display_order' => 0];
    protected $mediaFields = ['thumb', 'thumb_url'];
    protected $dates = ['deleted_at'];

    public $fillable = [];

    protected $guarded = ['widgets'];

    public $widgets = [];

    protected $search_fields = ['title'];
    /**
     * 实物
     */
    const REAL_GOODS = 1;
    /**
     * 虚拟物品
     */
    const VIRTUAL_GOODS = 2;

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'title' => '商品名称',
            'price' => '价格',
            'cost_price' => '成本价',
            'sku' => '商品单位',
            'thumb' => '图片',
            'stock' => '库存',
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
            'title' => 'required',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'sku' => 'required',
            'thumb' => 'required',
            'stock' => 'required|numeric|min:0',
        ];
    }


    public static function getList()
    {
        return static::uniacid();
    }

    public static function getGoodsById($id)
    {
        return static::find($id);
    }

    public function hasManyParams()
    {
        return $this->hasMany('app\common\models\GoodsParam');
    }

    public function belongsToCategorys()
    {
        return $this->hasMany('app\common\models\GoodsCategory', 'goods_id', 'id');
    }

    public function hasManyGoodsDiscount()
    {
        return $this->hasMany('app\common\models\GoodsDiscount');
    }

    public function hasManyOptions()
    {
        return $this->hasMany('app\common\models\GoodsOption');
    }

    public function hasOneBrand()
    {
        return $this->hasOne('app\common\models\Brand', 'id', 'brand_id');
    }

    public function hasOneShare()
    {
        return $this->hasOne('app\common\models\goods\Share');
    }

    public function hasOnePrivilege()
    {
        return $this->hasOne(self::getStaticNamespace().'\goods\Privilege');
    }

    public function hasOneGoodsDispatch()
    {
        return $this->hasOne('app\common\models\goods\GoodsDispatch');
    }

    public function hasOneDiscount()
    {
        return $this->hasOne('app\common\models\goods\Discount');
    }

    public function hasManyGoodsCategory()
    {
        return $this->hasMany('app\common\models\GoodsCategory', 'goods_id', 'id');
    }

    public function hasManySpecs()
    {
        return $this->hasMany('app\common\models\GoodsSpec');
    }

    public function hasOneSale()
    {
        return $this->hasOne(Sale::class, 'goods_id', 'id');
    }

    public function scopeIsPlugin($query)
    {
        return $query->where('is_plugin', 0);
    }

    public function scopeSearch($query, $filters)
    {
        $query->select([
            'yz_goods.*',
            'yz_goods_category.id as goods_category_id',
            'yz_goods_category.goods_id as goods_id',
            'yz_goods_category.category_id as category_id',
            'yz_goods_category.category_ids as category_ids',
        ])->uniacid()->isPlugin();

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
                    $query->where(status, $value);
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

                        $query->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')->whereRaw('FIND_IN_SET(?,category_ids)', [$id]);
                    } elseif(strpos($value, ',')){
                        $scope = explode(',', $value);
                        $query->join('yz_goods_category', function($join) use ($scope){
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereIn('yz_goods_category.category_id', $scope);
                        });
                    } else{
                        $query->join('yz_goods_category', function($join) use ($value){
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

    /**
     * @param $keyword
     * @return mixed
     */
    public static function getGoodsByName($keyword)
    {
        return static::uniacid()->select('id', 'title', 'thumb')
            ->where('title', 'like', '%' . $keyword . '%')
            ->get();
        //goods::update()
    }

    /**
     * @param $goodsId
     * @return mixed
     */
    public static function updatedComment($goodsId)
    {

        return self::where('id', $goodsId)
            ->update(['comment_num' => DB::raw('`comment_num` + 1')]);
    }

    /**
     * @author shenyang
     * 减库存
     * @param $num
     * @throws AppException
     */
    public function reduceStock($num)
    {
        if ($this->reduce_stock_method != 2) {
            if(!$this->stockEnough($num)){
                throw new AppException('下单失败,商品:' . $this->title . ' 库存不足');

            }
            $this->stock -= $num;

        }

    }

    /**
     * 库存是否充足
     * @author shenyang
     * @param $num
     * @return bool
     */
    public function stockEnough($num)
    {
        if ($this->reduce_stock_method != 2) {
            if ($this->stock - $num < 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 增加销量
     * @author shenyang
     * @param $num
     */
    public function addSales($num)
    {
        $this->real_sales += $num;
        $this->show_sales += $num;
    }

    /**
     * 判断实物
     * @author shenyang
     * @return bool
     */
    public function isRealGoods()
    {
        if (!isset($this->type)) {
            return false;
        }
        return $this->type == self::REAL_GOODS;
    }
}
