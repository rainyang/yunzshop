<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28
 * Time: 13:56
 */

namespace app\backend\modules\member\controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;

class PopularizePageShowController extends BaseController
{
    public function webSet()
    {
        $info = Setting::get("popularize.web");

        $info =  range(1,30);
        return view('member.popularize.index',[
            'info' => $info,
        ])->render();
    }
}