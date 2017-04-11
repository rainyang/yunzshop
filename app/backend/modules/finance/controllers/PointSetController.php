<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/10
 * Time: 下午2:00
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use Setting;
use app\common\helpers\Url;

class PointSetController extends BaseController
{
    /**
     * @name 积分基础设置
     * @author yangyang
     * @return array $set
     */
    public function index()
    {
        $set = Setting::get('point.set');
        //echo '<pre>';print_r($set);exit;
        $point_data = \YunShop::request()->set;
        if ($point_data) {
            //echo '<pre>';print_r($point_data);exit;
            if (Setting::set('point.set', $point_data)) {
                return $this->message('积分基础设置保存成功', Url::absoluteWeb('finance.point-set'));
            } else {
                $this->error('积分基础设置保存失败！！');
            }
        }

        return view('finance.point.set', [
            'set' => $set
        ])->render();
    }
}
