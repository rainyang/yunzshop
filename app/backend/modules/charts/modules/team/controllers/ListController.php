<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/1/11
 * Time: 11:49
 */
namespace app\backend\modules\charts\modules\team\controllers;



use app\backend\modules\member\models\MemberChildren;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class ListController extends BaseController
{
   public function index(){
       $search = \YunShop::request()->search;
       $pageSize = 20;
       $list = MemberChildren::getTeamCount($search) ->paginate($pageSize);
       ////$list = MemberChildren::getTeamCount($search) ->get();
       dd($list->toArray());
       $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

       return view('charts.team.list', [
           'list' => $list->toarray(),
           'pager' => $pager,
           'total' => $list->total(),
           'search' => $search,
       ])->render();
   }
}