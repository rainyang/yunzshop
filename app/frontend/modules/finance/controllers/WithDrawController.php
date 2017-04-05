<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 下午9:07
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\modules\finance\models\Withdraw;

class WithDrawController extends ApiController
{
    public function withdrawLog()
    {
        $status = \YunShop::request()->status;
        $request = Withdraw::getWithdrawLog($status)->get();
        if ($request) {
            return $this->successJson('获取数据成功!', $request->toArray());
        }
        return $this->errorJson('未检测到数据!');
    }
}