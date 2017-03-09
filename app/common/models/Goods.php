<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\backend\modules\goods\observers\GoodsObserver;
use HaoLi\LaravelAmount\Traits\AmountTrait;


class Goods extends BaseModel
{
    use AmountTrait;

    public $table = 'yz_goods';
    public $attributes = ['display_order' => 0];
    protected $mediaFields = ['thumb', 'thumb_url'];
    //public $display_order = 0;
    //protected $appends = ['status'];
    

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->mediaFields)) {
            //$value = tomedia($value);
        }
        parent::setAttribute($key, $value);
    }

    public $fillable = [];

    protected $guarded = ['widgets'];

    public $appends = [''];

    public $widgets = [];

    protected $amountFields = ['price', 'market_price', 'cost_price'];

    protected $search_fields = ['title'];

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
        return $this->hasOne('app\common\models\GoodsCategory');
    }

    public function hasManySpecs()
    {
        return $this->hasMany('app\common\models\GoodsSpec');
    }

    public function scopeSearch($query, $filters)
    {
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'category':
                    $category[] = ['id' => $value * 1];
                    $query->where('category_id', $category);
                    break;
                case 'conditions':
                    $query->where('condition', 'LIKE', $value);
                    break;
                case 'brands':
                    $query->where('brand_id', '=', $value);
                    break;
                default:
                    if ($key != 'category_name' && $key != 'search' && $key != 'page') {
                        //changing url encoded character by the real ones
                        $value = urldecode($value);
                        //applying filter to json field
                        $query->whereRaw("features LIKE '%\"".$key.'":%"%'.str_replace('/', '%', $value)."%\"%'");
                    }
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
        return static::where('title', 'like', $keyword.'%')
            ->get()
            ->toArray();
    }

    /**
     * 在boot()方法里注册下模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();

        //static::$booted[get_class($this)] = true;
        // 开始事件的绑定...
        //creating, created, updating, updated, saving, saved,  deleting, deleted, restoring, restored.
        /*static::creating(function (Eloquent $model) {
            if ( ! $model->isValid()) {
                // Eloquent 事件监听器中返回的是 false ，将取消 save / update 操作
                return false;
            }
        });*/

        //注册观察者
        static::observe(new GoodsObserver);
    }
}
