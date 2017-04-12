<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/12
 * Time: 下午2:35
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\services\finance\PointService;
use app\frontend\models\Member;
use app\frontend\modules\finance\models\PointLog;

class PointSummaryController extends ApiController
{
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $point_total = Member::getMemberById($member_id)['credit1'];

        $list = PointLog::getPointLogList($member_id)->limit(5)->get();
        $this->attachedServiceType($list);
        return $this->successJson('成功',
            [
                'point_total'       => $point_total,
                'list'              => $list,
            ]
        );
    }

    private function attachedServiceType($list)
    {
        if ($list) {
            foreach ($list as $key => $log) {
                switch ($log->point_mode) {
                    case PointService::POINT_MODE_GOODS:
                        $log->point_mode = PointService::POINT_MODE_GOODS_ATTACHED;
                        break;
                    case PointService::POINT_MODE_ORDER:
                        $log->point_mode = PointService::POINT_MODE_ORDER_ATTACHED;
                        break;
                    case PointService::POINT_MODE_POSTER:
                        $log->point_mode = PointService::POINT_MODE_POSTER_ATTACHED;
                        break;
                    case PointService::POINT_MODE_ARTICLE:
                        $log->point_mode = PointService::POINT_MODE_ARTICLE_ATTACHED;
                        break;
                    case PointService::POINT_MODE_ADMIN:
                        $log->point_mode = PointService::POINT_MODE_ADMIN_ATTACHED;
                        break;
                    case PointService::POINT_MODE_BY:
                        $log->point_mode = PointService::POINT_MODE_BY_ATTACHED;
                        break;
                }
            }
        }
    }
}