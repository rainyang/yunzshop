<?php
namespace app\backend\modules\finance\services;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 下午3:13
 */
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