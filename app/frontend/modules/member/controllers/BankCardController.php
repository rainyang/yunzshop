<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午5:24
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\member\controllers;


use app\common\components\ApiController;
use app\frontend\modules\member\models\MemberBankCard;

class BankCardController extends ApiController
{
    public function isHaveBankCard()
    {
        $bankCard = MemberBankCard::where('member_id', $this->getMemberId())->first();
        if ($bankCard && $bankCard->member_name && $bankCard->bank_card) {
            return $this->successJson('ok', ['status' => true]);
        }
        return $this->successJson('ok', ['status' => false]);
    }


    public function show()
    {
        $bankCard = MemberBankCard::where('member_id', $this->getMemberId())->first();

        !$bankCard && $bankCard = new MemberBankCard();

        $data = [
            'member_name' => $bankCard->member_name ?: "",
            'bank_card'   => $bankCard->bank_card ?: "",
            'bank_name'   => $bankCard->bank_name ?: "",
        ];

        return $this->successJson('ok',$data);
    }


    public function edit()
    {
        $bankCard = MemberBankCard::where('member_id', $this->getMemberId())->first();

        !$bankCard && $bankCard = new MemberBankCard();

        $member_name = \YunShop::request()->member_name;
        $bank_card = \YunShop::request()->bank_card;
        $bank_name = \YunShop::request()->bank_name;
        if ($bank_name && $bank_card && $member_name) {
            //$post = json_decode($post);
            $bankCard->member_id = \YunShop::app()->getMemberId();
            $bankCard->member_name = $member_name;
            $bankCard->bank_card = $bank_card;
            $bankCard->bank_name = $bank_name;
            $bankCard->is_default  = 1;
            $bankCard->uniacid     = \YunShop::app()->uniacid;


            $validator = $bankCard->validator();
            if ($validator->fails()) {
                return $this->errorJson($validator->messages()->first());
            }
            if (!$bankCard->save()) {
                return $this->errorJson('银行卡数据更新失败');
            }
            return $this->successJson('银行卡信息更新成功');
        }

        $data = [
            'member_name' => $bankCard->member_name,
            'bank_card'   => $bankCard->bank_card,
            'bank_name'   => $bankCard->bank_name,
        ];

        return $this->successJson('ok',$data);
    }



    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }


}
