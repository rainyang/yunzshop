<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/8/8
 * Time: 13:33
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


use Yunshop\TeamRewards\common\models\TeamRewardsMemberModel;

class DepositSetting extends BaseSetting
{
    public function canUse()
    {

        return $this->depositEnough();
    }

    public function exist()
    {

        return $this->depositEnough();
    }

    private function depositEnough()
    {
        if (!app('plugins')->isEnabled('team-rewards')) {
            return false;
        }
        $memberId = \YunShop::app()->getMemberId();
        $pluginMember = TeamRewardsMemberModel::uniacid()->where('member_id',$memberId)->first();
        if($pluginMember && $pluginMember->deposit > $this->orderPay->amount)
        {
            return true;
        }
        return false;
    }
}