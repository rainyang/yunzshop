<?php
namespace app\frontend\modules\goods\controllers;

use app\backend\modules\goods\models\Brand;
use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
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
class GoodsController extends ApiController
{
    public function getGoods()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            $this->errorJson('请传入正确参数.');
        }
        //$goods = new Goods();
        $goodsModel = Goods::uniacid()->with(['hasManyParams' => function ($query) {
            return $query->select('goods_id', 'title', 'value');
        }])->with(['hasManySpecs' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'description');
        }])->with(['hasManyOptions' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
        }])
        ->with('hasOneShare')
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
            $this->errorJson('商品已下架.');
        }

        $goodsModel->content = html_entity_decode($goodsModel->content);

        if ($goodsModel->has_option) {
            $goodsModel->min_price = $goodsModel->hasManyOptions->min("product_price");
            $goodsModel->max_price = $goodsModel->hasManyOptions->max("product_price");
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

    public function searchGoods()
    {
        $requestSearch = \YunShop::request()->search;

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item) && $item !== 0;
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        //dd($requestSearch);

        $list = Goods::Search($requestSearch)->select('*', 'yz_goods.id as goods_id')->where("status", 1)->orderBy('display_order', 'desc')->orderBy('id', 'desc')->paginate(20)->toArray();
        if (empty($list)) {
            $this->errorJson('没有找到商品.');
        }
        $this->successJson('成功', $list);
    }

    public function getGoodsCategoryList()
    {
        $category_id = intval(\YunShop::request()->category_id);

        if (empty($category_id)) {
            $this->errorJson('请输入正确的商品分类.');
        }

        $categorys = Category::uniacid()->select("name", "thumb", "id")->where(['id' => $category_id])->first();
        $goodsList = Goods::uniacid()->select('yz_goods.id','yz_goods.id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')
            ->where("category_id", $category_id)
            ->where('status', '1')
            ->orderBy('display_order', 'desc')
            ->orderBy('yz_goods.id', 'desc')
            ->paginate(20)->toArray();

        $categorys->goods = $goodsList;

        if (empty($categorys)) {
            $this->errorJson('此分类下没有商品.');
        }
        $this->successJson('成功', $categorys);
    }

    public function getGoodsBrandList()
    {
        $brand_id = intval(\YunShop::request()->brand_id);

        if (empty($brand_id)) {
            $this->errorJson('请输入正确的品牌id.');
        }

        $brand = Brand::uniacid()->select("name", "logo", "id")->where(['id' => $brand_id])->first();
        if (!$brand) {
            $this->errorJson('没有此品牌.');
        }
        //dd($brand);
        $goodsList = Goods::uniacid()->select('id','id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('status', '1')
            ->where('brand_id', $brand_id)
            ->orderBy('display_order', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)->toArray();

        if (empty($brand)) {
            $this->errorJson('此品牌下没有商品.');
        }

        $brand->goods = $goodsList;

        $this->successJson('成功', $brand);
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