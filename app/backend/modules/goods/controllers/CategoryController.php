<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午1:51
 */

namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;

class CategoryController extends BaseController
{
    public function index()
    {
        $list = CategoryService::getLists();
        ///echo "<pre>"; print_r($list);
        //或者模板路径可写全  $this->render('order/display/index',['list'=>$list]);
        //以下为简写
        $this->render('list', [
            'list' => $list
        ]);
    }
}