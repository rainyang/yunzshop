<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/29
 * Time: 9:51
 */

namespace app\backend\modules\setting\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\OperationLog;
use app\common\helpers\PaginationHelper;

class OperationLogController extends BaseController
{
    public function index()
    {

        $requestSearch = \YunShop::request()->search;
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';// && $item !== 0;
            });

        }



        $list = OperationLog::Search($requestSearch)->orderBy('id', 'decs')->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('setting.operation.log', [
            'list' => $list,
            'pager' => $pager,
            'search' => $requestSearch,
        ])->render();

    }
}