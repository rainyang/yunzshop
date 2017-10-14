<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 下午3:28
 */

namespace app\frontend\modules\finance\models;

use app\frontend\models\Member;
use app\frontend\models\MemberCoin;

class MemberPoint extends MemberCoin
{
    private $member;
    function __construct($uid)
    {
        $this->member = Member::whereUid($uid)->first();
    }

    /**
     * 获取最多可用积分
     * @return mixed
     */
    public function getMaxUsablePoint()
    {
        return $this->member->credit1;
    }
}