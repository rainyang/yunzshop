<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 下午9:27
 */

namespace app\frontend\modules\finance\services;

class WithdrawService
{
    public static function createStatusService($withdraw)
    {

        switch ($withdraw->status) {
            case -1:
                return '无效';
                break;
            case 0:
                return '未审核';
                break;
            case 1:
                return '未打款';
                break;
            case 2:
                return '已打款';
                break;
        }
    }
}