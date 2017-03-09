<?php
namespace app\frontend\modules\goods\controllers;

use app\common\components\BaseController;
use app\common\models\Category;
use app\common\models\Goods;
use app\common\models\GoodsCategory;
use app\common\models\GoodsSpecItem;
use app\frontend\modules\goods\services\GoodsService;

/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/3
 * Time: 22:16
 */
class GoodsController extends BaseController
{
    public function getGoods()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            $this->errorJson('请传入正确参数.');
        }
        //$goods = new Goods();
        $goodsModel = Goods::with(['hasManyParams' => function ($query) {
            return $query->select('goods_id', 'title', 'value');
        }])->with(['hasManySpecs' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'description');
        }])->with('hasOneShare')
            ->with('hasOneDiscount')
            ->with('hasOneGoodsDispatch')
            ->with('hasOnePrivilege')
            ->with(['hasOneBrand' => function ($query) {
                return $query->select('id', 'name');
            }])
            ->find($id);

        if (!$goodsModel) {
            $this->errorJson('商品不存在.');
        }

        if (!$goodsModel->status) {
            //$this->errorJson('商品已下架.');
        }

        $goodsModel->setHidden(
            [
                'deleted_at',
                'created_at',
                'updated_at',
                'cost_price',
                'real_sales',
                'is_deleted',
                'reduce_stock_method',
            ]);
        if ($goodsModel->thumb_url) {
            $goodsModel->thumb_url = unserialize($goodsModel->thumb_url);
        }

        //dd($goodsModel);
        foreach ($goodsModel->hasManySpecs as &$spec) {
            $spec['specitem'] = GoodsSpecItem::select('id', 'title', 'specid', 'thumb')->where('specid', $spec['id'])->get();
        }

        //return $this->successJson($goodsModel);
        $this->successJson('成功', $goodsModel);
    }

    public function getGoodsCategoryList()
    {
        //$category_array = \YunShop::request()->category_id ? ['id' => \YunShop::request()->category_id] : [];
        $category_id = intval(\YunShop::request()->category_id);
        if (empty($category_id)) {
            $this->errorJson('请输入正确的商品分类.');
        }
        $goodsList = Category::uniacid()->where(['id' => $category_id])->with(
            ['goodsCategories' => function ($query) {
                return $query->select(['goods_id', 'category_id'])->with(
                    [
                        'goods' => function ($query1) {
                            return $query1->select(['id', 'title', 'thumb', 'price', 'market_price'])->where('status', '1');
                        }
                    ]);
            }])->first();

        if ($goodsList) {
            $goodsList = $goodsList->goodsCategories->filter(function ($item) {
                return $item->goods != null;
            })->all();
        }

        //dd($goodsList);

        if (empty($goodsList)) {
            $this->errorJson('此分类下没有商品.');
        }
        $this->successJson('成功', $goodsList);
    }

    /**
     * @param $goods_id
     * @param null $option_id
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function getGoodsCart()
    {

        $goodsService = new GoodsService();
        $goods = $goodsService->getGoodsByCart(\YunShop::request()->goods_id, \YunShop::request()->option_id);
        if (!$goods) {
            return false;
        }

        return $goods;
    }

}