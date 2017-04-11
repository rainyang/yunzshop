<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/11
 * Time: 下午9:12
 */

namespace app\backend\modules\member\models;

use app\common\components\BaseController;

class MemberRelationApplyController extends BaseController
{
    private $pageSize = 20;

    public function __construct()
    {
        $list = Member::getMembers()
            ->paginate($this->pageSize)
            ->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        if (empty($starttime) || empty($endtime)) {
            $starttime = strtotime('-1 month');
            $endtime   = time();
        }

        return view('member.index', [
            'list' => $list,
            'endtime' => $endtime,
            'starttime' => $starttime,
            'total' => $list['total'],
            'pager' => $pager,
            'opencommission'=>1
        ])->render();
    }

    public function index()
    {}

    public function search()
    {}

    public function export()
    {}
}