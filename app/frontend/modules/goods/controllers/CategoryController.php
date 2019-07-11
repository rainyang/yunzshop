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

class CategoryController extends BaseController
{
    public function getCategory()
    {

        $set = Setting::get('shop.category');
        $pageSize = 100;
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        $list = Category::getCategorys($parent_id)->pluginId()->where('enabled', 1)->paginate($pageSize)->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }
        $recommend = $this->getRecommentCategoryList();

        $data = [
            'category' => $list,
            'recommend' => $recommend,
            'ads' => $this->getAds(),
            'set' => $set
        ];

        if($list['data']){
            return $this->successJson('获取分类数据成功!', $data);
        }
        return $this->errorJson('未检测到分类数据!',$data);
    }

    public function getAds()
    {
        $slide = [];
        $slide = Slide::getSlidesIsEnabled()->get();
        if ($slide) {
            $slide = $slide->toArray();
            foreach ($slide as &$item) {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $slide;
    }

    public function getRecommentCategoryList()
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
        $set = Setting::get('shop.category');
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
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $list['set'] = $set;
        if($list){
            return $this->successJson('获取子分类数据成功!', $list);
        }
        return $this->errorJson('未检测到子分类数据!',$list);
    }

    public function searchGoodsCategory()
    {
        $set = Setting::get('shop.category');
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