<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 上午11:44
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\backend\modules\finance\models\PointLog as PoinLogModel;
use app\common\helpers\PaginationHelper;

class PointLogController extends BaseController
{
    public function index()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;
        $list = PoinLogModel::getPointLogList($search)->paginate($pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('finance.point.point_log', [
            'list'          => $list,
            'pager'         => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList(),
            'search'        => $search
        ])->render();
    }
}