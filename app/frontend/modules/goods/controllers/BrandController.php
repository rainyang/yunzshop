<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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

use app\frontend\modules\goods\models\Brand;
use app\frontend\modules\goods\services\BrandService;

class BrandController extends ApiController
{
    public function getBrand()
    {
        $pageSize = 10;
        $list = Brand::getBrands()->paginate($pageSize)->toArray();
        if($list['data']){
            foreach ($list['data'] as &$item) {
                $item['logo'] = replace_yunshop(tomedia($item['logo']));
            }
            return $this->successJson('获取品牌数据成功!', $list);
        }
        throw new \app\common\exceptions\ShopException('未检测到品牌数据!', $list);
    }
}