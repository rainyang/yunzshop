<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/26
 * Time: 下午9:32
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;

class DemoController extends BaseController
{
    public function index()
    {
        return view('order.pay', [
        ])->render();
    }
}