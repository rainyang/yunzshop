<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 下午9:07
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\BaseController;
use app\frontend\modules\finance\models\Withdraw;

class WithDrawController extends BaseController
{
    public function withdrawLog()
    {
        $request = Withdraw::getWithdrawLog();
        dd($request->get()->toArray());
    }
}