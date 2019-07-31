<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Slide;
use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Session\Store;

use app\frontend\modules\goods\models\Category;
use app\frontend\modules\goods\services\CategoryService;

use app\common\models\Goods;
use app\common\models\GoodsSpecItem;

class CategoryController extends BaseController
{
    public function getCategory()
    {
        $pageSize = 100;
        $parent_id = \YunShop::request()->parent_id ?: '0';
        $list = Category::getCategorys($parent_id)->pluginId()->where('enabled', 1)->paginate($pageSize)->toArray();

        if (!$list['data']) {
            return $this->errorJson('未检测到分类数据!');
        }

        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $this->successJson('获取分类数据成功!', $list);
    }

    public function categoryHome()
    {
        $set = \Setting::get('shop.category');
        $pageSize = 100;
        $parent_id = \YunShop::request()->parent_id ?: '0';
        $list = Category::getCategorys($parent_id)->pluginId()->where('enabled', 1)->paginate($pageSize)->toArray();

        if (!$list['data']) {
            return $this->errorJson('未检测到分类数据!');
        }

        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));

        $recommend = $this->getRecommendCategoryList();
        // 获取推荐分类的第一个分类下的商品返回
        if (!empty($recommend)) {
            $goods_list = $this->getGoodsList($recommend[0]['id']);
        } else {
            $goods_list = [];
        }

        return $this->successJson('获取分类数据成功!', [
            'category' => $list,
            'recommend' => $recommend,
            'goods_list' => $goods_list,
            'ads' => $this->getAds(),
            'set' => $set
        ]);
    }

    public function getAds()
    {
        $slide = Slide::getSlidesIsEnabled()->get();
        if (!$slide->isEmpty()) {
            $slide = $slide->toArray();
            foreach ($slide as &$item) {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $slide;
    }

    /*
     * 通过某个分类id获取分类下的商品
     */
    public function getGoodsListByCategoryId()
    {
        $category_id = \YunShop::request()->category_id;
        return $this->successJson('获取商品成功!', $this->getGoodsList($category_id));
    }

    /**
     * 获取分类下的商品和规格
     */
    public function getGoodsList($category_id)
    {
        $list = Goods::uniacid()
            ->with(['hasManyParams' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'description');
        }, 'hasManyOptions' => function ($query) {
                return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
            }])
            ->search(['category'=>$category_id])->where('yz_goods.status',1)->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')
            ->get();
        $list->map(function(Goods $goodsModel){
            $goodsModel->buyNum = 0;
            if (strexists($goodsModel->thumb, 'image/')) {
                $goodsModel->thumb = yz_tomedia($goodsModel->thumb,'image');
            } else {
                $goodsModel->thumb = yz_tomedia($goodsModel->thumb);
            }

            foreach ($goodsModel->hasManySpecs as &$spec) {

                if ($spec['id']) {

                    $spec['specitem'] = GoodsSpecItem::select('id', 'title', 'specid', 'thumb')->where('specid', $spec['id'])->get();

                    foreach ($spec['specitem'] as &$specitem) {
                        $specitem['thumb'] = yz_tomedia($specitem['thumb']);
                    }
                }
            }

            if ($goodsModel->hasManyOptions && $goodsModel->hasManyOptions->toArray()) {
                foreach ($goodsModel->hasManyOptions as &$item) {
                    $item->thumb = replace_yunshop(yz_tomedia($item->thumb));
                }
            }
            if ($goodsModel->has_option) {
                $goodsModel->min_price = $goodsModel->hasManyOptions->min("product_price");
                $goodsModel->max_price = $goodsModel->hasManyOptions->max("product_price");
                $goodsModel->stock = $goodsModel->hasManyOptions->sum('stock');
            }
        });
        return  $list->toArray();
    }

    /**
     * 获取推荐分类
     * @return mixed
     */
    public function getRecommendCategoryList()
    {
        $request = Category::getRecommentCategoryList()
            ->where('is_home', '1')
            ->pluginId()
            ->get()
            ->toArray();
        foreach ($request as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $request;
    }
    
    public function getChildrenCategory()
    {
        $pageSize = 100;
        $set = \Setting::get('shop.category');
        $parent_id = intval(\YunShop::request()->parent_id);
        $list = Category::getChildrenCategorys($parent_id,$set)->paginate($pageSize)->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
            foreach ($item['has_many_children'] as &$has_many_child) {
                $has_many_child['thumb'] = replace_yunshop(yz_tomedia($has_many_child['thumb']));
                $has_many_child['adv_img'] = replace_yunshop(yz_tomedia($has_many_child['adv_img']));
            }
        }

        // 增加分类下的商品返回。
        // 逻辑为：点击一级分类，如果三级分类未开启，则将一级分类下的第一个二级分类的商品返回
        // 如果开启三级分类，则取三级分类的第一个分类下的商品返回
        if (!empty($list['data'])) {
            if (!empty($list['data'][0]['has_many_children'])) {
                $list['goods_list'] = $this->getGoodsList($list['data'][0]['id']);
            } else {
                $list['goods_list'] = $this->getGoodsList($list['data'][0]['has_many_children'][0]['id']);
            }
        }
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $list['set'] = $set;
        if($list){
            return $this->successJson('获取子分类数据成功!', $list);
        }
        return $this->errorJson('未检测到子分类数据!',$list);
    }

    public function searchGoodsCategory()
    {
        $set = \Setting::get('shop.category');
        $json_data = [];
        $list = Category::getCategorys(0)->pluginId()->where('enabled', 1)->get()->toArray();
        foreach ($list as &$parent) {
            $parent['son'] = Category::getChildrenCategorys($parent['id'],$set)->get()->toArray();
            foreach ($parent['son'] as &$value) {
                $value['thumb'] = replace_yunshop(yz_tomedia($value['thumb']));
                $value['adv_img'] = replace_yunshop(yz_tomedia($value['adv_img']));
                if (!is_null($value['has_many_children'])) {
                    foreach ($value['has_many_children'] as &$has_many_child) {
                        $has_many_child['thumb'] = replace_yunshop(yz_tomedia($has_many_child['thumb']));
                        $has_many_child['adv_img'] = replace_yunshop(yz_tomedia($has_many_child['adv_img']));
                    }
                } else {
                    $value['has_many_children'] = [];
                }
            }
            $parent['thumb'] = replace_yunshop(yz_tomedia($parent['thumb']));
            $parent['adv_img'] = replace_yunshop(yz_tomedia($parent['adv_img']));
        }

        return $this->successJson('获取子分类数据成功!', $list);
    }

//    public function getCategorySetting()
//    {
//        $set = Setting::get('shop.category');
//        if($set){
//            return $this->successJson('获取分类设置数据成功!', $set);
//        }
//        return $this->errorJson('未检测到分类设置数据!',$set);
//    }
    /**
     * 商城快速选购展示分类
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function fastCategory(){

        $list = Category::select('id', 'name', 'thumb', 'adv_img', 'adv_url')->uniacid()->where('level',1)->where('parent_id',0)->get();
        $list->map(function($category){
            $category->childrens = Category::select('id', 'name', 'thumb', 'adv_img', 'adv_url')->where('level',2)->where('parent_id',$category->id)->get();
        });

        if($list->isEmpty()){
            throw new AppException('未检测到分类数据');
        }
        return $this->successJson('获取分类成功!',['list'=>$list->toArray()]);
    }
}