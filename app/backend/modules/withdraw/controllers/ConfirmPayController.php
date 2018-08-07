<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/27 下午4:02
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\models\Withdraw;
use app\common\services\withdraw\PayedService;

class ConfirmPayController extends PreController
{
    /**
     * 提现记录确认打款接口
     */
    public function index()
    {
        $result = (new PayedService($this->withdrawModel))->confirmPay();

        if ($result == true) {
            return $this->message('确认打款成功', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->message('确认打款失败，请刷新重试', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]), 'error');
    }


    public function validatorWithdrawModel($withdrawModel)
    {
        if ($withdrawModel->status != Withdraw::STATUS_AUDIT && $withdrawModel->status != Withdraw::STATUS_PAYING) {
            throw new ShopException('状态错误，不符合打款规则！');
        }
    }

}
