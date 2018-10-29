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

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                if (is_array($item)) {
                    return !empty($item[0]);
                }
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }


        $list = OperationLog::Search($requestSearch)->pluginId()->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

    }
}