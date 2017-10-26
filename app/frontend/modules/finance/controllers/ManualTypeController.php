<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/26 下午2:19
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\frontend\models\MemberShopInfo;
use app\frontend\modules\member\models\MemberBankCard;

class ManualTypeController extends ApiController
{
    public function isCanSubmit()
    {
        $manual_type = Setting::get('withdraw.income')['manual_type'] ?: 1;

        switch ($manual_type) {
            case 2:
                $result['manual_type'] = 'wechat';
                $result['status'] = $this->getWeChatStatus();
                break;
            case 3:
                $result['manual_type'] = 'alipay';
                $result['status'] = $this->getAlipayStatus();
                break;
            default:
                $result['manual_type'] = 'bank';
                $result['status'] = $this->getBankStatus();

        }
        return $this->successJson('ok',$result);
    }

    private function getWeChatStatus()
    {
        $yzMember = MemberShopInfo::select('wechat')->where('member_id',\YunShop::app()->getMemberId())->first();
        return $yzMember ? $yzMember->wechat ? true : false : false;
    }

    private function getAlipayStatus()
    {
        $yzMember = MemberShopInfo::select('alipayname','alipay')->where('member_id',\YunShop::app()->getMemberId())->first();
        return $yzMember ? ($yzMember->alipayname && $yzMember->alipay) ? true : false : false;
    }


    private function getBankStatus()
    {

        $bankCard = MemberBankCard::select('member_name','bank_card','bank_name','bank_province','bank_city','bank_branch')
            ->where('member_id', \YunShop::app()->getMemberId())->first();

        if ($bankCard->member_name &&
            $bankCard->bank_card &&
            $bankCard->bank_name &&
            $bankCard->bank_province &&
            $bankCard->bank_city &&
            $bankCard->bank_branch
        ) {
            return true;
        }
        return false;
    }




}
