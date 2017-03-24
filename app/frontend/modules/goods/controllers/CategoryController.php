<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;


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
        $pageSize = 10;
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        $list = Category::getCategorys($parent_id)->paginate($pageSize)->toArray();
        if($list['data']){
            return $this->successJson('获取分类数据成功!', $list);
        }
        return $this->errorJson('未检测到分类数据!',$list);
    }
}