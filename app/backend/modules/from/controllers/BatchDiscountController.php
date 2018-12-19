<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14
 * Time: 15:28
 */

namespace app\backend\modules\from\controllers;


use app\common\components\BaseController;

class BatchDiscountController extends BaseController
{
    public function index()
    {
        return view('from.discount')->render();
    }

    public function store()
    {
        return view('from.discount')->render();
    }

}