<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:26
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberHistory;


class MemberHistoryController extends BaseController
{
    public function index()
    {
        $memberId = \YunShop::app()->getMemberId();
        $memberId = 96;

        $historyList = MemberHistory::getMemberHistoryList($memberId);
        return $this->successResult('', $historyList);
    }

    public function store()
    {
        //需要考虑添加过的只修改时间，不重复添加记录
        $memberId = 96;
        $goodsId = 100;

        $requestHistory = MemberHistory::hasMemberHistory($memberId, $goodsId);
        if ($requestHistory) {
            //修改updated_at
        } else {
            $result = MemberHistory::saveMemberHistory($memberId, $goodsId);
            if ($result) {
                return $this->successResult();
            }
        }
        return $this->errorResult();
    }
    public function destory()
    {

    }
}
