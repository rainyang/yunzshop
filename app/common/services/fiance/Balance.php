<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 上午10:31
 */

namespace app\common\services\fiance;


use app\common\models\Member;

class Balance
{
    /*
     * 修改会员余额
     *
     * @params int $memberId 会员ID
     * @params int $balance 改变金额值
     *
     * @return bool
     * @Author yitian */
    public function balanceChange($memberId, $balance)
    {
        $memberModel = Member::getMemberById($memberId);
        if ($memberModel) {
            $memberModel->credit2 += $balance;
            if ($memberModel->save()) {
                return true;
            }
        }
        return false;
    }

}