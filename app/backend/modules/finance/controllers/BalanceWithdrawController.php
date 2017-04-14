<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/14
 * Time: 下午5:06
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Withdraw;
use app\common\components\BaseController;
use app\common\facades\Setting;

class BalanceWithdrawController extends BaseController
{
    private $withdrawModel;


    private $withdrawPoundage;



    public function detail()
    {
        //$this->withdrawMath();




        $this->attachedMode();

        echo '<pre>'; print_r($this->getExamine()); exit;

        return view('finance.balance.withdraw', [
            'item' => $this->withdrawModel->toArray(),
            'examine' => $this->getExamine(),
        ])->render();
    }

    public function updateStatus()
    {

    }

    //获取去提现手续费设置
    private function withdrawSet()
    {
        $withdrawSet = Setting::get('withdraw.balance');
        $this->withdrawPoundage = $withdrawSet['poundage'];
    }

    //余额提现手续费N元
    private function withdrawPoundageMath()
    {
        return round(floatval($this->withdrawModel->amounts * $this->withdrawPoundage), 2);
    }

    private function getExamine()
    {
        return array(
            'examine_money' => $this->withdrawModel->amounts,
            'poundage'      => $this->withdrawPoundageMath(),
            'result_money'  => $this->withdrawModel->amounts - $this->withdrawPoundageMath()

        );
    }

    private function withdrawShackles()
    {

    }


    private function attachedMode()
    {
        $this->withdrawModel = Withdraw::getBalanceWithdrawById(\YunShop::request()->id) ?: new Withdraw();
    }

    private function getData()
    {

    }
}
