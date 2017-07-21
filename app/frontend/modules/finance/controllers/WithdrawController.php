<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/30
 * Time: 下午9:07
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\modules\finance\models\Withdraw;

class WithdrawController extends ApiController
{
    public function withdrawLog()
    {
        $status = \YunShop::request()->status;
        $request = Withdraw::getWithdrawLog($status)->orderBy('created_at', 'desc')->get();
        if ($request) {
            return $this->successJson('获取数据成功!', $request->toArray());
        }
        throw new \app\common\exceptions\ShopException('未检测到数据!');
    }

    public function withdrawInfo()
    {
        $id = \YunShop::request()->id;
        $request = Withdraw::getWithdrawInfoById($id)->first();

        if ($request) {

            return $this->successJson('获取数据成功!', $request->toArray());
        }
        throw new \app\common\exceptions\ShopException('未检测到数据!');
    }
}