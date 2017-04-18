<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/17
 * Time: 下午5:14
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;

class BalanceTransfer extends ApiController
{
    //

    //

    //


    /**
     * 会员余额转让接口
     * @return \Illuminate\Http\JsonResponse
     * @Author yitian */
    public function transfer()
    {
// todo 增加消息通知
        $financeSet = Setting::get('finance.balance');
        if ($financeSet['transfer'] == 1) {
            $transfer = \YunShop::app()->getMemberId();
            $recipient = \YunShop::request()->recipient;
            $transferMoney = trim(\YunShop::request()->transfer_money);

            $recipientModel = Member::getMemberById($recipient);
            $transferModel = Member::getMemberById($transfer);
            if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $transferMoney) || $transferModel->credit2 < $transferMoney) {
                return $this->errorJson('转让金额必须是大于0且大于您的余额，允许两位小数');
            }
            if ($transfer == $recipient) {
                return $this->errorJson('受让人不可以是您自己');
            }
            if (!$recipientModel || !$recipientModel) {
                return $this->errorJson('未获取到被转让者ID或被转让者不存在');
            }
            if ($transferMoney && $transfer && $recipient) {
                $data = array(
                    'member_id'     => $transfer,
                    'change_money'  => $transferMoney,
                    'operator'      => BalanceRecharge::PAY_TYPE_MEMBER,
                    'operator_id'   => $transfer,
                    'remark'        => '会员ID' . $transfer . '转让会员ID' . $recipient . '余额'. $transferMoney .'元',
                    'service_type'  => \app\common\models\finance\Balance::BALANCE_TRANSFER,

                    'recipient_id' => $recipient
                );

                $result = (new Balance())->changeBalance($data);
                if ($result == true) {
                    return $this->successJson('余额转让成功');
                }
                return $this->errorJson($result);
            }
            return $this->errorJson('请求数据错误，未进行余额转让操作');
        }
        return $this->errorJson('未开启余额转让功能');

    }
}
