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

        $list = PointLog::getPointLogList($member_id)->limit(5)->get();
        
        return $this->successJson('成功',
            [
                'point_total'       => $point_total,
                'list'              => $list,
            ]
        );
    }
}