<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/22 下午2:10
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


class PointToLoveService
{
    public function handleTransferQueue($uniacid)
    {
        \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;

        $result = $this->activationStart();
        if ($result !== true ) {
            \Log::info('--积分自动转入爱心值Uniacid:'.$uniacid.'自动转入失败--');
        }
        //\Setting::set('love.last_month_activation',date('m'));
        //\Setting::set('love.last_week_activation',date('W'));
        \Setting::set('point.last_to_love_time',date('d'));
        \Log::info('--积分自动转入爱心值Uniacid:'.$uniacid.'自动转入完成--');
    }


    private function activationStart()
    {


        return true;
    }

}
