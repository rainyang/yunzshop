<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/27
 * Time: 下午4:49
 */

namespace app\backend\modules\balance\controllers;


use app\common\components\BaseController;

class BalanceController extends BaseController
{
    /*
     * 余额基础设置
     *
     * */
    public function index()
    {
        return view('balance.index', [
            'list' => '',
            'pager' => '',
        ])->render();
    }

}
