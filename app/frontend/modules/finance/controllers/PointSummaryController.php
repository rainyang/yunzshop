<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/12
 * Time: 下午2:35
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\frontend\models\Member;
use app\frontend\modules\finance\models\PointLog;

class PointSummaryController extends ApiController
{
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $point_total = Member::getMemberById($member_id)['credit1'];

        $income_point = PointLog::getPointTotal($member_id, 1)->get()->sum('point');
        $cost_point = PointLog::getPointTotal($member_id, -1)->get()->sum('point');

        return $this->successJson('成功',
            [
                'point_total'       => $point_total,
                'income_point'      => $income_point,
                'cost_point'        => $cost_point,
                'last_income_time'  => PointLog::getLastTime($member_id, 1)->created_at,
                'last_cost_time'    => PointLog::getLastTime($member_id, -1)->created_at
            ]
        );
    }
}