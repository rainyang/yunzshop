<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/3
 * Time: 上午7:20
 */

namespace app\frontend\modules\member\models;


class smsSendLimitModel
{
    public $table = 'yz_sms_send_limit';

    public function getMobileInfo($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)
                   ->where('mobile', $mobile)
                   ->first()
                   ->toArray();
    }
}