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

use app\frontend\modules\goods\models\Brand;
use app\frontend\modules\goods\services\BrandService;

class BrandController extends BaseController
{
    public static function getBrand()
    {
        $pageSize = 1;
        $list = Brand::getBrands($pageSize);

        dd($list->toArray());
        return $list->toJson();
    }
}