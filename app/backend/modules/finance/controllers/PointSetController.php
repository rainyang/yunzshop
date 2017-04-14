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
        $point_data = \YunShop::request()->set;
        $enoughs_data = \YunShop::request()->enough;
        $give = \YunShop::request()->give;
        if (!empty($enoughs_data)) {
            foreach ($enoughs_data as $key => $value) {
                //echo '<pre>';print_r(floatval($enoughs_data[$key]));exit;
                $enough = floatval($value);
                //echo '<pre>';print_r($enough);exit;
                if ($enough > 0) {
                    $enoughs[] = array('enough' => floatval($enoughs_data[$key]), 'give' => floatval($give[$key]));
                }
            }
            $point_data['enoughs'] = $enoughs;
            //echo '<pre>';print_r($point_data);exit;
        }
        if ($point_data) {
            $point_data = $this->verifySetData($point_data);
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

    private function verifySetData($point_data)
    {
        $point_data['money'] = floatval($point_data['money']);
        $point_data['money_max'] = floatval($point_data['money_max']);
        $point_data['give_point'] = trim($point_data['give_point']);
        $point_data['enough_money'] = floatval($point_data['enough_money']);
        $point_data['enough_point'] = floatval($point_data['enough_point']);
        return $point_data;
    }
}
