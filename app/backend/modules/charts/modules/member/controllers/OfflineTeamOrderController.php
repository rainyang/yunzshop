<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;

use app\backend\modules\charts\modules\member\models\MemberLowerOrder;
use app\backend\modules\charts\modules\member\models\TeamOrder;
use app\common\helpers\PaginationHelper;
use app\backend\modules\charts\modules\member\services\LowerCountService;
use app\backend\modules\charts\modules\member\services\TeamOrderService;
use Illuminate\Support\Facades\DB;

class OfflineTeamOrderController extends OfflineCountController
{
    public function index()
    {
        $pageSize = 10;
        $sql = '(SELECT a.uniacid,b.uniacid as order_uniacid,a.member_id,sum(b.price) as order_price,COUNT(b.uid) as pay_count,sum(b.goods_total) as order_goods_total,
                 COUNT(a.child_id) as team_next_count,c.uid,c.nickname,c.avatar from ims_yz_member_children as a LEFT JOIN ims_yz_order as b on a.child_id = b.uid and b.status>=1 INNER JOIN 
                 ims_mc_members as c ON a.member_id = c.uid GROUP BY a.member_id ORDER BY team_next_count desc) as cc';
        $list = DB::table(DB::raw($sql))->paginate($pageSize);
        $search = \YunShop::request()->search;
        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.member.offline_team_order', [
            'page' => $page,
            'search' => $search,
            'list' => $list,
        ])->render();
    }

    public function res()
    {
        (new TeamOrderService())->memberOrder();
        dd(ok);
    }
}
