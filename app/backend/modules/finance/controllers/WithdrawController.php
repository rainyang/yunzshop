<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 上午11:28
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;

class WithdrawController extends BaseController
{
    public function set()
    {
        $set = Setting::get('withdraw.balance');

        $requestModel = \YunShop::request()->withdraw;
        if ($requestModel) {
            $inSet = fslse;
            foreach ($requestModel as $key => $item) {
                Setting::set('withdraw.' . $key, $item);
            }
            return $this->message('设置保存成功', Url::absoluteWeb('finance.withdraw.set'));
        }

        return view('finance.withdraw.withdraw-set', [
            'set' => $set
        ])->render();
    }
}