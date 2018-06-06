<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/30
 */
namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use app\common\helpers\Url;
use app\common\models\SearchFiltering;


class FilteringController extends ApiController
{
    
    function index()
    {
        $filtering = SearchFiltering::where('parent_id', 0)->where('is_show', 0)->get();

        foreach ($filtering as $key => &$value) {
            $value['value'] = SearchFiltering::select('id', 'parent_id', 'name')->where('parent_id', $value->id)->get()->toArray();
        }
        
        $this->successJson('获取过滤数据', $filtering->toArray());
    }
}