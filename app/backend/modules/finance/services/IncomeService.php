<?php
namespace app\backend\modules\finance\services;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 下午3:13
 */
class IncomeService
{
    public static function createStatusService($income)
    {

        switch ($income->status) {
            case -1:
                return '无效';
                break;
            case 0:
                return '未提现';
                break;
            case 1:
                return '已提现';
                break;
        }
    }
}