<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
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
        $pageSize = 10;
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        $list = Category::getCategorys($parent_id)->where('enabled', 1)->paginate($pageSize)->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = tomedia($item['thumb']);
        }
        $list['set'] = $set;
        if($list['data']){
            return $this->successJson('获取分类数据成功!', $list);
        }
        return $this->errorJson('未检测到分类数据!',$list);
    }
    
    public function getChildrenCategory()
    {
        $pageSize = 10;
        $set = Setting::get('shop.category');
        $parent_id = intval(\YunShop::request()->parent_id);
        $list = Category::getChildrenCategorys($parent_id,$set)->paginate($pageSize)->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = tomedia($item['thumb']);
            foreach ($item['has_many_children'] as &$has_many_child) {
                $has_many_child['thumb'] = tomedia($has_many_child['thumb']);
            }
        }
        $list['set'] = $set;
        if($list){
            return $this->successJson('获取子分类数据成功!', $list);
        }
        return $this->errorJson('未检测到子分类数据!',$list);
    }

//    public function getCategorySetting()
//    {
//        $set = Setting::get('shop.category');
//        if($set){
//            return $this->successJson('获取分类设置数据成功!', $set);
//        }
//        return $this->errorJson('未检测到分类设置数据!',$set);
//    }
}