<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/5/18
 * Time: 下午17:28
 */

namespace app\backend\modules\goods\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;

class FullAmountReduceController extends BaseController
{
    public function index()
    {
        $fullAmountReduceSet = array_pluck(\Setting::getAllByGroup('fullAmountReduce')->toArray(), 'value', 'key');
        $fullAmountReduceFreightSet = array_pluck(\Setting::getAllByGroup('fullAmountReduceFreight')->toArray(), 'value', 'key');
        return view('goods.fullAmountReduce.index', [
            'fullAmountReduceSet' => $fullAmountReduceSet,
            'fullAmountReduceFreightSet' => $fullAmountReduceFreightSet
        ])->render();
    }
    public function store(){
        $fullAmountReduceFreightSet = request()->input('fullAmountReduceFreightSet');
        foreach ($fullAmountReduceFreightSet as $key => $item) {
            \Setting::set('fullAmountReduceFreight.' . $key, $item);
        }
        return $this->message("设置保存成功",Url::absoluteWeb('goods.full-amount-reduce.index'));
    }
}