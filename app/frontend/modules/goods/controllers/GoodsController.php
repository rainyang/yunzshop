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
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class GoodsController extends ApiController
{
    public function getGoods()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            return $this->errorJson('请传入正确参数.');
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
            return $this->errorJson('商品不存在.');
        }

        if (!$goodsModel->status) {
            return $this->errorJson('商品已下架.');
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
        if ($goodsModel->thumb) {
            $goodsModel->thumb = replace_yunshop(tomedia($goodsModel->thumb));
        }
        if ($goodsModel->thumb_url) {
            $thumb_url = unserialize($goodsModel->thumb_url);
            foreach ($thumb_url as &$item) {
                $item = replace_yunshop(tomedia($item));
            }
            $goodsModel->thumb_url = $thumb_url;
        }
        foreach ($goodsModel->hasManySpecs as &$spec) {
            $spec['specitem'] = GoodsSpecItem::select('id', 'title', 'specid', 'thumb')->where('specid', $spec['id'])->get();
            foreach ($spec['specitem'] as &$specitem) {
                $specitem['thumb'] = replace_yunshop(tomedia($specitem['thumb']));
            }
        }
        // todo 商品详情挂件
        if (\Config::get('goods_detail')) {
            foreach (\Config::get('goods_detail') as $key_name => $row) {
                $row_res = $row['class']::$row['function']($id, true);
                if ($row_res) {
                    $goodsModel->$key_name = $row_res;
                }
            }
        }

        if($goodsModel->hasOneShare){
            $goodsModel->hasOneShare->share_thumb = replace_yunshop(tomedia($goodsModel->hasOneShare->share_thumb));
        }
        $this->setGoodsPluginsRelations($goodsModel);
        //return $this->successJson($goodsModel);
        return $this->successJson('成功', $goodsModel);
    }
    private function setGoodsPluginsRelations($goods){
        $goodsRelations = app('GoodsManager')->tagged('GoodsRelations');
        collect($goodsRelations)->each(function($goodsRelation) use($goods){
            $goodsRelation->setGoods($goods);
        });
    }
    public function searchGoods()
    {
        $requestSearch = \YunShop::request()->search;

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }
        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';
        
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
        $list = Goods::Search($requestSearch)->select('*', 'yz_goods.id as goods_id')
            ->where("status", 1)
            ->where("plugin_id", 0)
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function($rows) {
                return collect($rows)->map(function($item, $key) {
                    if ($key == 'thumb' && preg_match('/^images/', $item)) {
                        return replace_yunshop(tomedia($item));
                    } else {
                        return $item;
                    }
                });
            })->toArray();
            $list['data'] = $data;
        }

        if (empty($list)) {
            return $this->errorJson('没有找到商品.');
        }
        return $this->successJson('成功', $list);
    }

    public function getGoodsCategoryList()
    {
        $category_id = intval(\YunShop::request()->category_id);

        if (empty($category_id)) {
            return $this->errorJson('请输入正确的商品分类.');
        }

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';

        $categorys = Category::uniacid()->select("name", "thumb", "id")->where(['id' => $category_id])->first();
        $goodsList = Goods::uniacid()->select('yz_goods.id','yz_goods.id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')
            ->where("category_id", $category_id)
            ->where('status', '1')
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        $categorys->goods = $goodsList;

        if (empty($categorys)) {
            return $this->errorJson('此分类下没有商品.');
        }
        return $this->successJson('成功', $categorys);
    }

    public function getGoodsBrandList()
    {
        $brand_id = intval(\YunShop::request()->brand_id);
        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';


        if (empty($brand_id)) {
            return $this->errorJson('请输入正确的品牌id.');
        }

        $brand = Brand::uniacid()->select("name", "logo", "id")->where(['id' => $brand_id])->first();

        if (!$brand) {
            return $this->errorJson('没有此品牌.');
        }
        $goodsList = Goods::uniacid()->select('id','id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('status', '1')
            ->where('brand_id', $brand_id)
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        if (empty($brand)) {
            return $this->errorJson('此品牌下没有商品.');
        }

        $brand->goods = $goodsList;

        return $this->successJson('成功', $brand);
    }


}