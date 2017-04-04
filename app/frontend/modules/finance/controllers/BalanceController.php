<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\finance\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\ApiController;
use app\common\models\finance\BalanceTransfer;
use app\common\services\fiance\Balance;
use Monolog\Handler\IFTTTHandler;

class BalanceController extends ApiController
{
    /*
     * 转让接口
     *
     * @Author yitian */
    public function transfer()
    {
// todo 增加消息通知
        $transferor = \YunShop::app()->getMemberId();
        $recipient = \YunShop::request()->recipient;
        $transferMoney = trim(\YunShop::request()->transfer_money);
        //$transferMoney = '1.11';
        $transferor = 55;
        $recipient = 57;

        $recipientModel = Member::getMemberById($recipient);
        $transferorModel = Member::getMemberById($transferor);

        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $transferMoney) || $transferorModel->credit2 < $transferMoney) {
            return $this->errorJson('转账金额必须是大于0且大于您的余额的两位小数数值');
        }
        if ($transferor == $recipient) {
            return $this->errorJson('受让人不可以是您自己');
        }
        if (!$recipientModel || !$recipientModel) {
            return $this->errorJson('未获取到被转让者ID或被转让者不存在');
        }
        //转账记录
        $balanceTransferMOdel = new BalanceTransfer();
        $data = array(
            'uniacid' => \YunShop::app()->uniacid,
            'transferor' => $transferor,
            'recipient' => $recipient,
            'money' => $transferMoney,
            'status' => 0
        );
        $balanceTransferMOdel->fill($data);
        $validator = $balanceTransferMOdel->validator();
        if ($validator->fails()) {
            return $this->errorJson($validator->messages());
        }
        if ($balanceTransferMOdel->save()) {
            //dd('转让记录写入成功');
            $balanceCharge = new Balance();
            if ($balanceCharge->balanceChange($transferor,-$transferMoney) && $balanceCharge->balanceChange($recipient,$transferMoney)) {
                dd('余额修改成功');
            }
        }
    }

    /*
     * 会员余额转让记录
     *
     * @Author yitian */
    public function transferRecord()
    {
        $memberId = \YunShop::app()->getMemberId();
        $memberId= '55';
        if ($memberId) {
            $transferRecord = BalanceTransfer::getMemberTransferRecord($memberId);
            return $this->successJson('转让记录获取成功', $transferRecord);
        }
        return $this->errorJson('未获取到会员ID');
    }

    /*
     * 会员余额被转让记录
     *
     * @Author yitian */
    public function recipientRecord()
    {
        $memberId = \YunShop::app()->getMemberId();
        $memberId= '55';
        if ($memberId) {
            $recipientRecord = BalanceTransfer::getMemberRecipientRecord($memberId);
            return $this->successJson('被转让记录获取成功', $recipientRecord);
        }
        return $this->errorJson('未获取到会员ID');
    }

}