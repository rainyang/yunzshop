<?php
namespace app\frontend\modules\shop\controllers;

use app\common\components\BaseController;
use app\common\models\Category;
use app\common\models\Goods;
use app\common\models\GoodsCategory;
use app\common\models\GoodsSpecItem;
use app\frontend\modules\goods\services\GoodsService;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/3
 * Time: 22:16
 */
class IndexController extends BaseController
{
    public function getDefaultIndex()
    {
        $data = [
            'ads' => $this->getAds(),
            'category' => $this->getRecommentCategoryList(),
            'goods' => $this->getRecommentGoods(),
        ];
        $this->successJson('成功', $data);
    }

    public function getRecommentGoods()
    {
        //$goods = new Goods();
        $field = ['id as goods_id', 'thumb', 'title', 'price', 'market_price'];
        $goodsList = Goods::uniacid()->select(DB::raw(implode(',', $field)))
            ->where("is_recommand", 1)
            ->where("status", 1)
            ->get();

        //dd($goodsList->toArray());
        return $goodsList;
    }

    public function getRecommentCategoryList()
    {
        return [];
    }

    /**
     * @param $goods_id
     * @param null $option_id
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function getAds()
    {

        return [];
    }

}