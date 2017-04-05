<?php
namespace app\frontend\modules\finance\models;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 上午9:40
 */

class Withdraw extends \app\common\models\Withdraw
{
    public static function getWithdrawLog($status)
    {
        $withdrawModel = self::select('id', 'type_name', 'amounts', 'poundage', 'status', 'created_at');

        $withdrawModel->uniacid();

        $withdrawModel->where('member_id', \YunShop::app()->getMemberId());
        if ($status >= '0') {
            $withdrawModel->where('status', $status);
        }
        return $withdrawModel;
    }
}