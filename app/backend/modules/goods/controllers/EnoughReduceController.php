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

class EnoughReduceController extends BaseController
{
    public function index()
    {
        $setting = \Setting::getByGroup('enoughReduce');

        return view('goods.enoughReduce.index', [
            'setting' => json_encode($setting),
        ])->render();
    }

    public function store()
    {
        $setting = request()->input('setting');
        foreach ($setting as $key => $value) {
            if(is_array($value)){
                $value = $value;
            }
            \Setting::set('enoughReduce.' . $key, $value);
        }
        return $this->successJson("设置保存成功", Url::absoluteWeb('goods.enough-reduce.index'));
    }
}