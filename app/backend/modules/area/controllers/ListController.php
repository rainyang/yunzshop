<?php
namespace app\backend\modules\area\controllers;

use app\backend\modules\area\models\Area;
use app\common\components\BaseController;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/5/21
 * Time: 下午17:34
 */
class ListController extends BaseController
{

    public function index()
    {
        $cities = Area::getAreasByCity(\YunShop::request()->parent_id);
        return $this->successJson('成功',$cities);
    }

}