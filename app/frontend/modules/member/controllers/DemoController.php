<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/27
 * Time: 下午5:14
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;

class DemoController extends BaseController
{

    function index()
    {
        return view('order.pay', [
        ])->render();
    }
}