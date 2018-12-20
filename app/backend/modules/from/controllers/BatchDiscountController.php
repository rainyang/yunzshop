<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14
 * Time: 15:28
 */

namespace app\backend\modules\from\controllers;


use app\backend\modules\goods\models\Category;
use app\common\components\BaseController;

class BatchDiscountController extends BaseController
{
    public function index()
    {
        return view('from.discount')->render();
    }

    public function store()
    {
        $category = Category::getAllCategory();
//        dd($category);
        return view('from.set')->render();
    }

    public function selectCategory()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $category = Category::getCategorysByName($kwd);
//            dd($category);
            return $this->successJson('ok', $category);
        }
    }
}