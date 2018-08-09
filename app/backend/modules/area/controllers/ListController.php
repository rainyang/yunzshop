<?php

namespace app\backend\modules\area\controllers;

use app\backend\modules\area\models\Area;
use app\common\components\BaseController;
use app\common\models\Street;
use Illuminate\Database\Eloquent\Collection;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/5/21
 * Time: 下午17:34
 */
class ListController extends BaseController
{

    public function index()
    {
        if($this->isStreet(request('parent_id'))){
            $areas = Street::where('parentid',request('parent_id'))->get();
        }else{
            $areas = Area::where('parentid',request('parent_id'))->get();
        }

        return $this->successJson('成功', $this->formatAreas($areas));
    }
    private function isStreet($areaId){
        if($areaId == 0){
            return false;
        }
        $area = Area::find(request('parent_id'));

        if(!$area){
            return false;
        }
        return $area->level == 3;
    }
    private function formatAreas(Collection $areas)
    {
        if($areas->isEmpty()){
            return $areas;
        }
        // 不是子节点
        if (!$areas->first()->isLeaf()) {
            $areas->each(function (Area $area) {
                $area['children'] = [];
            });
        }
        return $areas;

    }
}