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
use app\frontend\modules\discount\services\models\GoodsDiscount;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Goods extends BaseModel
{

    use SoftDeletes;

    public $table = 'yz_goods';
    public $attributes = ['display_order' => 0];
    protected $mediaFields = ['thumb', 'thumb_url'];
    protected $dates = ['deleted_at'];

    public $fillable = [];

    protected $guarded = ['widgets'];

    public $appends = ['vip_price'];

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

    public function getVipPriceAttribute()
    {
        return GoodsDiscount::getVipPrice($this);
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
        return $this->hasOne('app\common\models\goods\Privilege');
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
        $query->uniacid()->isPlugin();

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
                    $query->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')->whereIn('yz_goods_category.category_id', $value);
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
     * 减库存
     * @param $num
     * @throws AppException
     */
    public function reduceStock($num)
    {
        if ($this->reduce_stock_method != 2) {
            if ($this->stock - $num < 0) {
                throw new AppException('下单失败,商品:' . $this->title . ' 库存不足');
            }
            $this->stock -= $num;
        }
    }

    /**
     * 判断实物
     */
    public function isRealGoods()
    {
        if(!isset($this->type)){
            return false;
        }
        return $this->type == self::REAL_GOODS;
    }
}
