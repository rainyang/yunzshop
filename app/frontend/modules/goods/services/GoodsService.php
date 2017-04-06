<?php
namespace app\frontend\modules\goods\services;

use app\common\events\order\BeforeOrderGoodsAddInOrder;
use app\frontend\modules\goods\models\Goods;
use app\frontend\modules\goods\services\models\factory\GoodsModelFactory;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午4:01
 */
class GoodsService
{
    public static function getGoodsModels($params)
    {
        $ids = array_column($params, 'goods_id');
        return Goods::whereIn('id',$ids)->withOption()->get();
    }

    /**
     * @param $goods_id
     * @param null $option_id
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function getGoodsByCart($goods_id, $option_id = null)
    {
        $goodsModel = Goods::with(['hasManyOptions' => function ($query) {
            return $query->select('goods_id', 'title', 'thumb', 'product_price', 'market_price');
        }])->select('id', 'thumb', 'price', 'market_price', 'title')->where('id', $goods_id)->first();

        if (!$goodsModel) {
            return false;
        }
        //dd($goodsModel);
        if ($option_id) {
            $goodsModel->option = $goodsModel->hasManyOptions->filter(function ($item) use ($option_id) {
                /*if ($item->id == $option_id) {
                    $specs = explode('_', $item->specs);
                    //没生效,明天看
                    $specs = array_where($specs, function($value){
                        return $value = GoodsSpecItem::find($value)->title;
                    });
                    $item->title = $specs;
                }*/
                return $item->id == $option_id;
            })->first()->toArray();
        }
        return $goodsModel->toArray();
    }

}